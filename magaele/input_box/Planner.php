<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Planner extends CI_Controller {

	public function __construct()
    {
		parent::__construct();

		require_once(APPPATH."controllers/BaseFunction.php");
		$this->load->model('Place_model');
		$this->load->model('Flight_model');
	}

	public function index()
	{
		getTokenTest();

	}

	public function destination()
	{

		$region = $this->input->get("regionId");
		$keyword = $this->input->get("keyword");
		$filter = false;
		$cfilter = false;
		$afilter = false;

		//當有關鍵字以關鍵字為主找區域
		if(isset($keyword) && $keyword != ""){
			$filter["name"] = $keyword;

			$countryList = $this->Place_model->getCountry(false, $filter);
			if(isset($countryList) && $countryList != false){
				$region = $countryList[0]["regionId"];

				$cfilter["name"] = $filter["name"];

			}

			//以旅遊區為準
			$travelAreaList = $this->Place_model->getTravelArea(false, $filter);
			if(isset($travelAreaList) && $travelAreaList != false){
				$region = $travelAreaList[0]["regionId"];
				$country = $travelAreaList[0]["countryId"];

				$afilter["name"] = $filter["name"];
				$cfilter["id"] = $country;
			}


		}
		else{
			$countryList = $this->Place_model->getCountry($region, false);
		}

		if(!isset($region) || $region == ""){
			$region = "A-NE01";
		}
		$this->smarty->assign("nowRegion", $region);


		$regionList = $this->Place_model->getRegion(false);
		$this->smarty->assign("regionList", $regionList);


		$countryList = $this->Place_model->getCountry($region, $cfilter);
		if(isset($countryList) && $countryList != false){
			for($i = 0; $i < count($countryList); $i++){

				$countryList[$i]["cityList"] = $this->Place_model->getTravelArea($countryList[$i]["id"], $afilter);
			}

		}


		$this->smarty->assign("countryList", $countryList);

		$keyCountryList = $this->Place_model->getCountry(false, false);
		$keyAreaList = $this->Place_model->getTravelArea(false, false);
		$nowCount = 0;
		for($i = 0; $i < count($keyCountryList); $i++){
			$searchKeywordList[$nowCount]["name"] = $keyCountryList[$i]["name"];
			$nowCount++;
		}
		for($i = 0; $i < count($keyAreaList); $i++){
			$searchKeywordList[$nowCount]["name"] = $keyAreaList[$i]["name"];
			$nowCount++;
		}
		$this->smarty->assign("searchKeywordList", $searchKeywordList);

		$itineraryDepartDate = date("Y/m/d");
		$this->smarty->assign("keyword", $keyword);
		$this->smarty->assign("itineraryDepartDate", $itineraryDepartDate);
		$this->smarty->assign("searchBarSwitch", false);
		$this->smarty->assign("ContentPage", "planner/destination.tpl");
        $this->smarty->view('master.tpl');
	}

	//replace display/old/view
	public function old($planId)
	{
		global $userSessionData;

		//get current schedule via $planId
		$filter['id'] = $planId;
		$filter['userid'] = $userSessionData['userId'];
		$filter['Entrance'] = 1;	//1:B2B 2:ERP
		$planData = getRequestV2('v2_OldPlanInfo', $filter);
		
		// check if the plan is valid and data exists, if not return error
		if (empty($planData->Data)) {
			echo '無行程資料，五秒後導入行程管理列表頁。';
			echo '<script type="text/javascript">setTimeout(function(){ window.location.href = "' . $GLOBALS["site_url"] . 'user/scheduleList"; }, 5000);</script>';
			exit;
		}

		//basic info
		$plan = $planData->Data->OldPlannerData; // equals to plan's data the old planData

		//N: 很重要，消極方式修復日期排序問題
		if ($plan->planList[0]->day !== 1) {
			$plan->planList = _::sortBy($plan->planList, function($obj) { return $obj->day; });
		}
		//N//

		$planId_info = explode('-', $planId);
		$planId_parent = $planId_info[0]; // get mother planId, get rid of the '-0001's

		$trip_id = $plan->id; // 多元推薦行程號
		$group_id = $plan->groupid; // 多元推薦團號
		$defaultDay = $plan->dayNum; //todo
		$itineraryTitle = $plan->name; //todo
		$itineraryDepartDate = date('Y/m/d', strtotime($plan->departDate));
		$itineraryReturnDate = date('Y/m/d', strtotime($plan->departDate . ' + ' . $defaultDay . ' day'));
		$originalPlanId = (empty($trip_id) ? null : $planId_info[0]); //判斷是不是有原先的ID (原本myView的判斷方式)

		$type = empty($this->input->get('type')) ? 'view' : $this->input->get('type');
		//end of basic info

		//確認是否有歷史編輯紀錄?
		$oldPlannerListData = null;
		$history_page = null;
		$history_total_pages = null;
		if (!empty($plan->lastId)) {
			$history_page = (empty($this->input->get('page')) ? 1 : $this->input->get('page'));

			$request_data['planId'] = $planId_parent;
			$request_data['userid'] = $userSessionData['userId'];
			$request_data['offset'] = $history_page;
			$request_data['max'] = '10';
			$oldPlannerList = getRequestV2('v2_UserOldPlanList', $request_data); // 取得歷史編輯行程的清單

			if (isset($oldPlannerList->Data) && !empty($oldPlannerList->Data) && isset($oldPlannerList->Data->UserPlanData)) {
                $oldPlannerListData = $oldPlannerList->Data->UserPlanData;
                $history_total_pages = ceil($oldPlannerList->Data->Total/10);
			} else {
                $history_total_pages = 1;
			}
		}

		//判斷是否已經加入比較
		$cartExists = false;
		if (!empty($trip_id)) {
			$cartTripIds = $this->session->userdata('pdmCompareCart');
			if (!empty($cartTripIds)) {
				$cartTripIds = explode(',', $cartTripIds);
				if (in_array($trip_id, $cartTripIds)) {
					$cartExists = true;
				}
			}
		}

		//加入比較
		$compare_trip = $this->session->userdata('pdmContent');
		$compare_trip = isset($compare_trip) ? array_keys(json_decode($compare_trip, TRUE)) : array();
		$this->smarty->assign('compare_trip', $compare_trip);

		//設定log_action_id
		$favorite_action_id = '17';
		$addcompare_action_id = '18';
		$addtrip_action_id = '19';

		//all sets
		$this->smarty->assign("isOldPlan", true);
		$this->smarty->assign('type', $type);
		$this->smarty->assign('planId', $planId);
		$this->smarty->assign('trip_id', $trip_id);
		$this->smarty->assign('group_id', $group_id);
		$this->smarty->assign('originalPlanId', $originalPlanId);

		$this->smarty->assign('planData', $plan);
		$this->smarty->assign('defaultDay', $defaultDay);
		$itineraryObj = '';
		$this->smarty->assign('itineraryObj', $itineraryObj);
		$this->smarty->assign('itineraryTitle', $itineraryTitle);
		$this->smarty->assign('itineraryDepartDate', $itineraryDepartDate);
		$this->smarty->assign('itineraryReturnDate', $itineraryReturnDate);

		$this->smarty->assign('oldPlannerListData', $oldPlannerListData); // 歷史比較
		$this->smarty->assign('history_page', $history_page); // 歷史比較
		$this->smarty->assign('history_total_pages', $history_total_pages); // 歷史比較

		$this->smarty->assign('cartExists', $cartExists); // 是否已加入購物車
		$this->smarty->assign('favorite_action_id', $favorite_action_id); // 多元推薦專用
        $this->smarty->assign('addcompare_action_id', $addcompare_action_id); // 多元推薦專用
        $this->smarty->assign('addtrip_action_id', $addtrip_action_id); // 多元推薦專用

		$this->smarty->assign('searchBarSwitch', false);
		//planeFlag 主要為新版行程使用，所以不在這編用
		//end of set methods

		$this->smarty->assign('ContentPage', 'planner/old_view.tpl');
		$this->smarty->view('master.tpl');
	}

	//replace display/new/view
	public function getCombinition($planId)
	{
		global $userSessionData;

		$itineraryTitle = "自訂行程";
		$itineraryDepartDate = "";
		$itineraryReturnDate = "";
		$destinationList = "";
		$planeFlag = false;
		$type = empty($this->input->get('type')) ? 'view' : $this->input->get('type');

		$planData = $this->apiGetCombinationPlanInfo($planId);

		if (empty($planData->Data)) {
			echo '無行程資料，五秒後導入行程管理列表頁。';
			echo '<script type="text/javascript">setTimeout(function(){ window.location.href = "' . $GLOBALS["site_url"] . 'user/scheduleList"; }, 5000);</script>';
			exit;
		}

		$planInfo = $planData->Data->PlannerData;
		$originPlanInfo = $planInfo;

		$defaultDay = $planInfo->dayNum;
		$itineraryDepartDate = date("Y/m/d", strtotime($planInfo->departDate));
		$itineraryReturnDate = date("Y/m/d", strtotime($planInfo->departDate." + ".$defaultDay." day"));

		$planeFlag = $this->input->get('planeFlag');
		$this->smarty->assign('planeFlagError', ($planeFlag == '0' ? 1 : 0));

		$itineraryTitle = $planInfo->name;
		$defaultDay = $planInfo->dayNum;

		$itineraryGenerate = new StdClass;
		$itineraryGenerate->scheduleList = [];
		$itineraryGenerate->flight = new StdClass;
		$itineraryGenerate->title = $planInfo->name;

		if(isset($planInfo->departDate)){
			$planInfo->departDate = date("Y/m/d", strtotime($planInfo->departDate));
		}
		$itineraryGenerate->departDate = $planInfo->departDate;
		$itineraryGenerate->days = $planInfo->dayNum;

		if(isset($planInfo->flight) && $planInfo->flight != false){
			$itineraryGenerate->flight = $planInfo->flight;
			$itineraryGenerate->flight->depart = $planInfo->flight->depart;
			$itineraryGenerate->flight->breturn = $planInfo->flight->back;
			$itineraryGenerate->flightTraffic = new StdClass;
			$planeFlag = true;
		}

		if(isset($planInfo->planList) && $planInfo->planList != false){
			foreach ($planInfo->planList as $key => $dayPlanInfo) { //列出每天行程
				$tmpDayPlan = new StdClass;
				$tmpDayPlan->startTime = "08:00";
				$tmpDayPlan->placeList = $dayPlanInfo->cityList;

				if(isset($dayPlanInfo->hotel) && $dayPlanInfo->hotel != null){
					$tmpDayPlan->hotelInfo["duration"] = 0;
					$tmpDayPlan->hotelInfo["id"] = "";
					$tmpDayPlan->hotelInfo["key"] = "1_".$dayPlanInfo->hotel->id;
					$tmpDayPlan->hotelInfo["pid"] = "1_".$dayPlanInfo->hotel->id;
					$tmpDayPlan->hotelInfo["name"] = $dayPlanInfo->hotel->name;
					$tmpDayPlan->hotelInfo["type"] = "hotel";

					$tmpDayPlan->hotelInfo["thumbnail"][0] = new StdClass;
					$tmpDayPlan->hotelInfo["thumbnail"][0]->img = $dayPlanInfo->hotel->thumbnail;
					$tmpDayPlan->hotelInfo["guideId"] = $dayPlanInfo->hotel->nowGuide->id;
					$tmpDayPlan->hotelInfo["guideList"] = $dayPlanInfo->hotel->guideList;
				}
				else{
					$tmpDayPlan->hotelInfo = false;
				}

				$tmpDayPlan->hotelcity = new StdClass;
				if(isset($dayPlanInfo->hotelcity) && $dayPlanInfo->hotelcity != null)
					$tmpDayPlan->hotelcity = $dayPlanInfo->hotelcity;

				if(isset($dayPlanInfo->itemList) && $dayPlanInfo->itemList){
					foreach ($dayPlanInfo->itemList as $itemkey => $itemInfo) { //列出行程item

						if($itemInfo->mode != 4){ //元組件
							$tmpPoiId = $itemInfo->id;

							if($itemInfo->category != "0"){
								$itemInfo->pid = "1_".$tmpPoiId;
							}
							else{
								$itemInfo->pid = "0_".$tmpPoiId;
							}
							$itemInfo->guideId = $itemInfo->nowGuide;
							$itemInfo->type = "poi";
							$itemInfo->name = specialStringReplace($itemInfo->name);


							$itemInfo->nowGuideInfo = false;
							if(isset($itemInfo->guideList)){
								for($g = 0; $g < count($itemInfo->guideList); $g++){
									if($itemInfo->guideList[$g]->id == $itemInfo->nowGuide){
										$itemInfo->nowGuideInfo = $itemInfo->guideList[$g];

									}
									$itemInfo->guideList[$g]->brief = specialStringReplace($itemInfo->guideList[$g]->brief);
								}
							}

							//如果是組件，要針對玩法丟latitude
							if($itemInfo->category != "0"){
								if(isset($itemInfo->location->latitude) && isset($itemInfo->location->longitude)){
									$itemInfo->latitude = $itemInfo->location->latitude;
									$itemInfo->longitude = $itemInfo->location->longitude;
								}
								else{
									$itemInfo->latitude = "";
									$itemInfo->longitude = "";
								}

								$itemInfo->startLatitude = $itemInfo->startLatitude;
								$itemInfo->startLongitude = $itemInfo->startLongitude;
								$itemInfo->endLatitude = $itemInfo->endLatitude;
								$itemInfo->endLongitude = $itemInfo->endLongitude;
							}
							else{
								if(isset($itemInfo->poiList)){
									$poiLength = count($itemInfo->poiList);
									$itemInfo->latitude = $itemInfo->poiList[0]->latitude;
									$itemInfo->longitude = $itemInfo->poiList[0]->longitude;
									$itemInfo->startLatitude = $itemInfo->startLatitude;
									$itemInfo->startLongitude = $itemInfo->startLongitude;
									$itemInfo->endLatitude = $itemInfo->endLatitude;
									$itemInfo->endLongitude = $itemInfo->endLongitude;
								}
							}


						}
						else{
							if(!isset($itemInfo->title))
								$itemInfo->title = "";

							if(!isset($itemInfo->addr))
								$itemInfo->addr = "";

							$itemInfo->id = "custom-".$key."-".$itemkey;
							$itemInfo->pid = "custom-".$key."-".$itemkey;
							$itemInfo->key = "custom-".$key."-".$itemkey;
							$itemInfo->type = "custom";
							$itemInfo->name = specialStringReplace($itemInfo->title);
							$itemInfo->place = $itemInfo->addr;

							$tmpDate = "";
							if(isset($itemInfo->astarttime) && $itemInfo->astarttime != 0){
								if(strlen($itemInfo->astarttime) < 4){
									$itemInfo->astarttime = "0".$itemInfo->astarttime;
								}

								$tmpDate = DateTime::createFromFormat("Hi", $itemInfo->astarttime);
								if(isset($tmpDate)){
									$tmpDate = $tmpDate->format("H:i");
								}

							}
							$itemInfo->startTime = $tmpDate;

							if(isset($itemInfo->aendtime) && $itemInfo->aendtime != 0){
								if(strlen($itemInfo->aendtime) < 4){
									$itemInfo->aendtime = "0".$itemInfo->aendtime;
								}

								$tmpDate = DateTime::createFromFormat("Hi", $itemInfo->aendtime);
								if(isset($tmpDate)){
									$tmpDate = $tmpDate->format("H:i");
								}
							}
							else{
								$tmpDate = "";
							}
							$itemInfo->endTime = $tmpDate;

							if($itemInfo->startTime != $itemInfo->endTime){
								$itemInfo->duration = (strtotime($itemInfo->endTime) - strtotime($itemInfo->startTime))/60;
							}

							if(isset($itemInfo->dispatch) && $itemInfo->dispatch == 1){
								$itemInfo->car = "1";
							}
							else{
								$itemInfo->car = "0";
							}

							if(!isset($itemInfo->contact)){
								$itemInfo->contact = "";
							}

							if(!isset($itemInfo->phone)){
								$itemInfo->phone = "";
							}

							if(!isset($itemInfo->desc)){
								$itemInfo->desc = "";
							}

							$itemInfo->contactName = $itemInfo->contact;
							$itemInfo->contactPhone = $itemInfo->phone;
							$itemInfo->memo = $itemInfo->desc;

							if($itemInfo->type == "2"){
								$itemInfo->spots = "1";
							}
							else{
								$itemInfo->spots = "0";
							}

							$itemInfo->latitude = "";
							$itemInfo->longitude = "";
							$itemInfo->latitudeLeave = "";
							$itemInfo->longitudeLeave = "";

							$itemInfo->guideId = "";
							$itemInfo->guideList = false;
							$itemInfo->thumbnail = "";

							//poi 經緯度
							// $itemInfo->

							$itemInfo->address = [];
							if(isset($itemInfo->off) && $itemInfo->off){
								for($c = 0; $c < count($itemInfo->off); $c++){

									$tmpArr = new stdclass;
									$tmpArr->latitude = "";
									$tmpArr->longitude = "";

									// $tmpArr->address = $itemInfo->off[$c]->onaddr;
									$tmpArr->address = $itemInfo->off[$c]->addr;

									if($itemInfo->car == "1"){
										// if(strlen($itemInfo->off[$c]->offdate) < 4){
										// 	$itemInfo->off[$c]->offdate = "0".$itemInfo->off[$c]->offdate;
										// }
										if(strlen($itemInfo->off[$c]->date) < 4){
											$itemInfo->off[$c]->date = "0".$itemInfo->off[$c]->date;
										}
										// $tmpDate = DateTime::createFromFormat("Hi", $itemInfo->off[$c]->offdate);
										$tmpDate = DateTime::createFromFormat("Hi", $itemInfo->off[$c]->date);
										$tmpDate = $tmpDate->format("H:i");
										$tmpArr->carTime = $tmpDate;
									}
									$itemInfo->address[] = $tmpArr;
								}
							}
						}
					}
					$tmpDayPlan->itemList = $dayPlanInfo->itemList;
				}
				$itineraryGenerate->scheduleList[] = $tmpDayPlan;
			}
		}
		$itineraryObj = json_encode($itineraryGenerate);

		$this->smarty->assign("planData", $originPlanInfo);

		$spyInit = 'data-spy="scroll" data-target="#navbar-day" data-offset="10"';
		$this->smarty->assign("spyInit", $spyInit);

		//設定log_action_id
		$favorite_action_id = '20';
		$addcompare_action_id = '21';
		$addtrip_action_id = '22';

		$this->smarty->assign("isOldPlan", false);
		$this->smarty->assign('type', $type);
		$this->smarty->assign("planeFlag", $planeFlag);
		$this->smarty->assign("planId", $planId);

		$this->smarty->assign("defaultDay", $defaultDay);
		$this->smarty->assign("itineraryObj", $itineraryObj);
		$this->smarty->assign("itineraryTitle", $itineraryTitle);
		$this->smarty->assign("itineraryDepartDate", $itineraryDepartDate);
		$this->smarty->assign("itineraryReturnDate", $itineraryReturnDate);

        $this->smarty->assign('favorite_action_id', $favorite_action_id);
        $this->smarty->assign('addcompare_action_id', $addcompare_action_id);
        $this->smarty->assign('addtrip_action_id', $addtrip_action_id);

		$this->smarty->assign("searchBarSwitch", false);

		$this->smarty->assign("ContentPage", "planner/combinition_view.tpl");

        $this->smarty->view('master.tpl');
	}

	public function getPoiList2() {

		global $userSessionData;

		$categoryId = $this->input->get('categoryId');
		$cityId = $this->input->get('cityId');
		$circleId = $this->input->get('circleId');
		$subCategory = $this->input->get('subCategory');
		$max = $this->input->get('max');
		$offset = $this->input->get('offset');
		$keyword = $this->input->get('keyword');
		$filter = [
			'userid' => $userSessionData['userId'],
			'categoryId' => $categoryId,
			'cityId' => $cityId,
			'offset' => $offset,
			'max' => $max,
			'keyword' => $keyword
		];
		if (!empty($circleId)){
			$filter['circleId'] = $circleId;
		}
		if (!empty($subCategory)){
			$filter['subCategory'] = $subCategory;
		}
		$poiData = getRequestV2('v2_SelectPoiList', $filter);

		if (!empty($poiData->Data)) {
			$poiList = $poiData->Data->PoiList;
			for ($i = 0; $i < count($poiList); $i++) {
				$typeKey = 1;
				if ($poiList[$i]->categoryId == 0)
					$typeKey = 0;

				$poiList[$i]->key = $typeKey . '_' . $poiList[$i]->id; //指標
			}
			$poiData->Data->PoiList = $poiList;
		}
		echo json_encode($poiData->Data, JSON_UNESCAPED_UNICODE);
	}

	public function getTourDetailInfo2() {
		global $userSessionData;

		$id = $this->input->get('id');
		$type = $this->input->get('type');

		$filter = [
			'id' => $id,
			'userid' => $userSessionData['userId']
		];

		if ($type == 0) {
			//組件
			$poiData = getRequestV2('v2_SelectPackageDetail', $filter);
		} else {
			//元件
			$poiData = getRequestV2('v2_SelectPoiDetail', $filter);
		}

		if (isset($poiData->Data) && $poiData->Data->Total > 0) {

			if ($type == 0)
				$detailData = $poiData->Data->PackageInfo;
			else
				$detailData = $poiData->Data->PoiInfo;

			$detailData->key = $type . '_' . $id;
			$detailData->thumbnail = $detailData->img;

			if (empty($detailData->guideList)) {
				if ($type == 0) {

					for ($i = 0; $i < count($detailData->guideList); $i++) {

						$cityId = $detailData->guideList[$i]->play->startCity;
						$cityInfo = $this->Place_model->getCityInfo($cityId, false);
						//假設找不到去另一個資料表找
						if(!isset($cityInfo) || $cityInfo == false){
							$cityInfo = $this->Place_model->getCityListInfo($cityId, false);
						}
						$detailData->guideList[$i]->play->startCityName = $cityInfo["name"];

						$cityId = $detailData->guideList[$i]->play->endCity;
						$cityInfo = $this->Place_model->getCityInfo($cityId, false);
						//假設找不到去另一個資料表找
						if(!isset($cityInfo) || $cityInfo == false){
							$cityInfo = $this->Place_model->getCityListInfo($cityId, false);
						}
						$detailData->guideList[$i]->play->endCityName = $cityInfo["name"];

						//組件要將玩法的時間處理
						$detailData->guideList[$i]->serviceTime = date("H:i", strtotime("2017-01-01 ".$detailData->guideList[$i]->play->serviceTimeStart))." ~ ".date("H:i", strtotime("2017-01-01 ".$detailData->guideList[$i]->play->serviceTimeEnd));
					}
				} else {
					for($i = 0; $i < count($detailData->guideList); $i++){
						$detailData->guideList[$i]->cityName = $detailData->guideList[$i]->startCity;
					}
				}
			}

			if(isset($detailData->relativePoi) && $detailData->relativePoi){
				foreach ($detailData->relativePoi as $key => $poiInfo) {
					$poiInfo->key = $type."_".$poiInfo->id;
				}
			}
		} else {
			$detailData = [];
		}

		echo json_encode($detailData, JSON_UNESCAPED_UNICODE);

	}

	public function getNearByPoiList2() {
		$nearbyData = getRequestV2('v2_SelectPoiDistance', [
			'id' => $this->input->get('id')
		]);

		if (isset($nearbyData->Data) && $nearbyData->Data->Total > 0) {
			echo json_encode($nearbyData->Data->PoiList);
		} else {
			echo json_encode([]);
		}
	}

	public function getNearByCityList2() {

		$cityId = $this->input->get('cityId');

		$cityData = getRequestV2('v2_SelectNearbyCity', ['cityNo' => $cityId]);

		$tmpCityList = $cityData->Data;

		$cityList = [
			'list' => false,
			'total' => 0
		];
		if (!empty($tmpCityList)) {
			if (!empty($tmpCityList->CityInfo)) {
 				$cityList['list'] = $tmpCityList->CityInfo->nearbyCircle;
				$cityList['total'] = $tmpCityList->Total;
			}
		}

		echo json_encode($cityList, JSON_UNESCAPED_UNICODE);
	}

	public function getZoneList2() {

		$cityId = $this->input->get('cityId');

		$cityData = getRequestV2('v2_SelectShoppingDistrict', [
			'cityNo' => $cityId
		]);

		$tmpCityList = $cityData->Data;
		$zoneList = [
			'list' => false,
			'total' => 0
		];
		if (!empty($tmpCityList)) {
			$zoneList['list'] = $tmpCityList->ShoppingDistrictList;
			$zoneList['total'] = $tmpCityList->Total;
		}

		echo json_encode($zoneList, JSON_UNESCAPED_UNICODE);
	}

	public function getAirportList2() {
		$cityId = $this->input->get("cityId");
		$type = $this->input->get("type");
		$keyword = $this->input->get("keyword");
		$keyword = urldecode($keyword);
		$areaId = $this->input->get('areaId');
		$countryId = $this->input->get("countryId");

		$airportList = getRequestV2('v2_SelectAirportList', [
			'offset' => 1,
			'max' => 25,
			'countryId' => $countryId,
			'cityId' => $cityId,
			'areaId' => $areaId
		]);

		if (isset($airportList->Data)) {

			$newList = [];
			foreach($airportList->Data->AirportList as $al) {

				$cityList = $this->Flight_model->getCityAndCountryNameByAirport($al->airport);
				if ($cityList === '') {
					$CityName = '';
					$AreaName = '';
				} else {
					$CityName = $cityList[0]['CityName'];
					$AreaName = $cityList[0]['AreaName'];
				}

				$newList[] = [
					'CityId' => $al->cityId,
					'CityName' => $CityName,
					'AreaName' => $AreaName,
					'AirportCode' => $al->airport,
					'airportList' => [
						[
							'AirportCode' => $al->airport,
							'type' => 1,
							'AreaName' => $CityName,
							'AirportName' => $al->name,
							'CityName' => $AreaName,
							'CityId' => $al->cityId,
						]
					]
				];
			}


			$payload = [
				'total' => $airportList->Data->Total,
				'list' => $newList
			];
		} else {
			$payload = [
				'total' => 0,
				'list' => []
			];
		}
		echo json_encode($payload);
	}

	public function getAirportList(){
		$cityId = $this->input->get("cityId");
		$type = $this->input->get("type");
		$keyword = $this->input->get("keyword");
		$keyword = urldecode($keyword);

		$countryId = $this->input->get("countryId");

		$filter["type"] = $type;

		if(isset($cityId) && $cityId != "" && $cityId != false){
			$filter["CityId"] = $cityId;
		}
		else{
			$filter["AreaName"] = $countryId;
		}

		if(isset($keyword) && $keyword != "" && $keyword != false){
			$filter["AreaName"] = $keyword;
		}

		$cityList = $this->Flight_model->getAirportCityList($filter);
		if($cityList != false){
			for($i = 0; $i < count($cityList); $i++){
				$afilter["CityId"] = $cityList[$i]["CityId"];
				$cityList[$i]["airportList"] = $this->Flight_model->getAirportList($afilter);
			}
		}
		else{
			// //如果該cityId找不到機場，改用areaId搜尋
			$filter2["type"] = $type;
			$filter2["AreaName"] = $countryId;
			$cityList = $this->Flight_model->getAirportCityList($filter2);
			if($cityList != false){
				for($i = 0; $i < count($cityList); $i++){
					$afilter["CityId"] = $cityList[$i]["CityId"];
					$cityList[$i]["airportList"] = $this->Flight_model->getAirportList($afilter);
				}
			}
		}

		if($cityList != false){
			$retData["total"] = count($cityList);
			$retData["list"] = $cityList;
		}
		else{
			$retData["total"] = 0;
			$retData["list"] = false;
		}
		echo json_encode($retData);
	}

	public function getFlightList(){
		$flightInfo = $this->input->post("flightInfo");
		$flightInfo = json_decode($flightInfo);

		$flightData = postJSONRequest("v2_SelectFlightsList", $flightInfo);

		$newFlightData = $flightData;
        if(!empty($newFlightData->Data)){
			$flightList = $newFlightData->Data->FlightsList;

			for($i = 0; $i < count($flightList); $i++){
				$tmpInfo = $newFlightData->Data->FlightsList[$i]->depart;
				$tmpInfo->airplaneName = $tmpInfo->airlineName;
				$newFlightData->Data->FlightsList[$i]->depart = [];
				$flightList[$i]->depart[] = $tmpInfo;
				$tmpInfo = $newFlightData->Data->FlightsList[$i]->breturn;
				$tmpInfo->airplaneName = $tmpInfo->airlineName;
				$flightList[$i]->breturn = [];
				$flightList[$i]->breturn[] = $tmpInfo;

			}
		}

		echo json_encode($newFlightData);
	}

	public function listPlan()
	{
		$this->smarty->assign("ContentPage", "planner/list.tpl");
        $this->smarty->view('master.tpl');
	}

	public function listFavorite()
	{
		$this->smarty->assign("ContentPage", "planner/listFavorite.tpl");
        $this->smarty->view('master.tpl');
	}

	public function favoriteAction()
	{
		global $userSessionData;

		$data["userId"] = $userSessionData["userId"];
		$data["poiId"] = (int)$this->input->post("poiId");
		$data["status"] = (int)$this->input->post("status");
		$data["type"] = (int)$this->input->post("type");

		$ret = postJSONRequest("v2_UpdateUserFavoritePoi", $data);

		echo json_encode($ret);
	}

	//replace display/old/edit
	public function oldEdit($planId, $type)
    {
	    global $userSessionData;

		// 初始宣告
        $itineraryTitle = "自訂行程";
        $itineraryDepartDate = "";
        $itineraryReturnDate = "";

		$filter2['id']          = $planId;
        $filter2['userid']      = $userSessionData['userId'];
        $filter2['Entrance'] = 1;	//1:B2B 2:ERP
        $planData = getRequestV2('v2_OldPlanInfo', $filter2);

        $oldPlannerListData = [];

        if(!empty($planData->Data))
        {
            $plan = $planData->Data->OldPlannerData;

			//N: 很重要，消極方式修復日期排序問題
			if ($plan->planList[0]->day !== 1) {
				$plan->planList = _::sortBy($plan->planList, function($obj) { return $obj->day; });
			}
			//N//

            $this->smarty->assign('trip_id', $plan->id);
            $this->smarty->assign('group_id', $plan->groupid);

            $defaultDay          = $plan->dayNum;
            $itineraryTitle      = $plan->name;
            $itineraryDepartDate = date("Y/m/d", strtotime($plan->departDate));
			$itineraryReturnDate = date("Y/m/d", strtotime($plan->departDate." + ".$defaultDay." day"));

			//avoid date format error
			if(isset($plan->cbmin->departDate))
				$plan->cbmin->departDate = date("Ymd", strtotime($plan->cbmin->departDate));

			//確認是否有歷史編輯紀錄
			if (!empty($plan->lastId))
			{
				$history_page = (empty($this->input->get('page')) ? 1 : $this->input->get('page'));

				$newPlanId = explode("-", $planId);

				$request_data['planId'] = $newPlanId[0];
				$request_data['userid'] = $userSessionData['userId'];
				$request_data['offset'] = $history_page;
				$request_data['max']    = '10';
				$oldPlannerList = getRequestV2('v2_UserOldPlanList', $request_data);

				if(isset($oldPlannerList->Data) && !empty($oldPlannerList->Data) && isset($oldPlannerList->Data->UserPlanData))
                {
                    $oldPlannerListData  = $oldPlannerList->Data->UserPlanData;
                    $history_total_pages = ceil($oldPlannerList->Data->Total/10);
                }else{
				    $history_total_pages = 1;
                }

				$this->smarty->assign('oldPlannerListData',  $oldPlannerListData);
				$this->smarty->assign('history_page',        $history_page);
				$this->smarty->assign('history_total_pages', $history_total_pages);
			}

        } else {
			echo '無行程資料，五秒後導入行程管理列表頁。';
			echo '<script type="text/javascript">setTimeout(function(){ window.location.href = "' . $GLOBALS["site_url"] . 'user/scheduleList"; }, 5000);</script>';
			exit;
            exit;
		}

        //判斷是否已經加入比較
        $cartExists = false;
        if (!empty($plan->id))
        {
            $cartTripIds = $this->session->userdata('pdmCompareCart');
            if (!empty($cartTripIds))
            {
                $cartTripIds = explode(',', $cartTripIds);
                if (in_array($plan->id, $cartTripIds))
                {
                    $cartExists = true;
                }
            }
        }
        $this->smarty->assign('cartExists', $cartExists);

        $itineraryObj = "";
        $this->smarty->assign("oldPlanData", $oldPlannerListData);

        //加入比較
        $compare_trip = $this->session->userdata('pdmContent');
        $compare_trip = isset($compare_trip) ? array_keys(json_decode($compare_trip, TRUE)) : array();
        $this->smarty->assign("compare_trip", $compare_trip);

        //設定log_action_id
        $favorite_action_id   = '17';
        $addcompare_action_id = '18';
		$addtrip_action_id    = '19';

		$this->smarty->assign("isOldPlan", true);
		$this->smarty->assign("type", $type);
        $this->smarty->assign("planId",    $planId);
        $this->smarty->assign("planData",  $plan);

        $this->smarty->assign("defaultDay", $defaultDay);
        $this->smarty->assign("itineraryObj", $itineraryObj);
        $this->smarty->assign("itineraryTitle", $itineraryTitle);
        $this->smarty->assign("itineraryDepartDate", $itineraryDepartDate);
        $this->smarty->assign("itineraryReturnDate", $itineraryReturnDate);

        $this->smarty->assign('favorite_action_id', $favorite_action_id);
        $this->smarty->assign('addcompare_action_id', $addcompare_action_id);
        $this->smarty->assign('addtrip_action_id', $addtrip_action_id);

		$this->smarty->assign("searchBarSwitch", false);

		$this->smarty->assign("ContentPage", "planner/oldEdit.tpl");

        $this->smarty->view('master.tpl');
	}

	//replace info
	public function editCombinition($planId, $type)
	{
		global $userSessionData, $lionTravel;

		$defaultDay = 0;
		$itineraryTitle = "自訂行程";
		$itineraryDepartDate = "";
		$destinationJson = $this->input->post("destinationList");
		$destinationList = $destinationJson;
		$continent = "";
		$country = "";
		$areaID = "";
		$areaName = "";
		$template = $this->input->get("template");
		$status = $this->input->get("status");
		//require form
		$planData = new StdClass;
		$planData->cbmin = new StdClass;

		// TODO: 需額外判定是否有旅遊區，有旅遊區要跳出選擇城市內頁
		$cityFlag = "false";
		if(isset($destinationList) && $destinationList != "") //有選擇出發地
		{
			$destinationList = json_decode($destinationList);
			for($i = 0; $i < count($destinationList->list); $i++)
			{
				$defaultDay += $destinationList->list[$i]->days;

				$tfilter["id"] = $destinationList->list[$i]->id;
				$areaInfo = $this->Place_model->getTravelArea(false, $tfilter);

				$continent = $areaInfo[0]["regionId"];
				$country = $areaInfo[0]["countryId"];
				$areaID = $areaInfo[0]["id"];

				$filter["type"] = 4;
				$filter["offset"] = 1;
				$filter["max"] = 1000;
				$filter["no"] = $destinationList->list[$i]->id;
				//$cityData = getRequest("temparea", $filter);
				$cityData = getRequestV2('v2_SelectArea', $filter);

				$cityList = false;
				if(isset($cityData->Data)){
					$tmpCityList = $cityData->Data->AreaList;
					if($tmpCityList != false){
						for($j = 0; $j < count($tmpCityList); $j++){
							$data = false;
							$data["id"] = $tmpCityList[$j]->id;
							$data["name"] = $tmpCityList[$j]->name;

							$tmpLocation = '{"latitude": '.$tmpCityList[$j]->latitude.',"longitude": '.$tmpCityList[$j]->longitude.'}';
							$data["location"] = $tmpLocation;
							$cityList[$j] = $data;
						}
					}
				}

				if(isset($cityList) && $cityList != false){

					//處理經緯度
					foreach ($cityList as $key => $city) {
						$cityList[$key]["latitude"] = getLatitude($city["location"]);
						$cityList[$key]["longitude"] = getLongitude($city["location"]);

						//判別有沒有機場
						$cfilter["cityId"] = $city["id"];
						$airportList = $this->Flight_model->getAirportCityList($cfilter);
						if($airportList != false)
							$cityList[$key]["airportStatus"] = true;
						else
							$cityList[$key]["airportStatus"] = false;

						if($key == 0){
							//選擇的國家
							$countryFilter["id"] = $country;
							$countryInfo = $this->Place_model->getCountry(false, $countryFilter);
							$areaName = $countryInfo[0]["name"];
						}
					}
					$cityFlag = "true";
				}
				$destinationList->list[$i]->cityList = $cityList;
			}

			if(isset($destinationList->info->title) && $destinationList->info->title != false)
				$itineraryTitle = $destinationList->info->title;

			if(isset($destinationList->info->departDate) && $destinationList->info->departDate != false)
				$itineraryDepartDate = $destinationList->info->departDate;
		}
		else //沒選擇出發地
			$destinationList = false;

		if($defaultDay == 0)
			$defaultDay = 1;

		//取得航空公司列表
		$airlineList = $this->Flight_model->getAirlineList(false);
		$this->smarty->assign("airlineList", $airlineList);

		if(!isset($userSessionData["userId"]) || $userSessionData["userId"] == false)
			redirect($lionTravel["loginURL"]);

		if($planId == "new")
		{
			$itineraryObj = "";
			$planData->planId = null;
			$planData->CBMId = null;
			$planData->PCMId = null;
			$planData->QuotesId = null;

			//set cbmin data
			if(!is_null($userSessionData["userInfo"]))
			{
				if($userSessionData["userInfo"]->userType == "agent")
				{
					// $planData->cbmin->contactName = $userSessionData["userInfo"]->name;
					// $planData->cbmin->phone = $userSessionData["userInfo"]->phone;
					// $planData->cbmin->mobile = $userSessionData["userInfo"]->mobile;
					// $planData->cbmin->email = $userSessionData["userInfo"]->email;
					// $planData->cbmin->salesName = $userSessionData["userInfo"]->salesInfo->name;
					// $planData->cbmin->salesPhone = $userSessionData["userInfo"]->salesInfo->phone;
					// $planData->cbmin->salesMobile = $userSessionData["userInfo"]->salesInfo->mobile;
					// $planData->cbmin->salesEmail = $userSessionData["userInfo"]->salesInfo->email;
					$planData->cbmin->contactName = '';
					$planData->cbmin->phone = '';
					$planData->cbmin->mobile = '';
					$planData->cbmin->email = '';
					$planData->cbmin->salesName = '';
					$planData->cbmin->salesPhone = '';
					$planData->cbmin->salesMobile = '';
					$planData->cbmin->salesEmail = '';
				}
			}
		}
		else
		{
			$planRet = $this->apiGetCombinationPlanInfo($planId);

			if (empty($planRet->Data)) {
				echo '無行程資料，五秒後導入行程管理列表頁。';
				echo '<script type="text/javascript">setTimeout(function(){ window.location.href = "' . $GLOBALS["site_url"] . 'user/scheduleList"; }, 5000);</script>';
				exit;
			}

			$planInfo = $planRet->Data->PlannerData;
			$originPlanInfo = $planInfo;
		
			$planData->planId = $planInfo->CBMId;
			$planData->CBMId = $planInfo->CBMId;
			$planData->PCMId = $planInfo->PCMId;
			$planData->QuotesId = $planInfo->QuotesId;

			$itineraryTitle = $planInfo->name;
			$itineraryDepartDate = date("Y/m/d", strtotime($planInfo->departDate));
			$defaultDay = $planInfo->dayNum;

			$itineraryGenerate = new StdClass;
			$itineraryGenerate->scheduleList = [];
			$itineraryGenerate->flight = new StdClass;
			$itineraryGenerate->title = $planInfo->name;
			$itineraryGenerate->days = $planInfo->dayNum;
			$itineraryGenerate->cityList = $planInfo->cityList;

			if(is_null($planInfo->cbmin->departDate))
				$planInfo->cbmin->departDate = date("Y/m/d", strtotime("now"));
			else
				$planInfo->cbmin->departDate = date("Y/m/d", strtotime($planInfo->cbmin->departDate));
			$itineraryGenerate->departDate = $planInfo->cbmin->departDate;

			//$continent = $planInfo->Continent;
			//$country = $planInfo->Country;
			$areaID = $planInfo->areaId;

			//選擇的國家
			$countryFilter["id"] = $country;
			$countryInfo = $this->Place_model->getCountry(false, $countryFilter);
			$areaName = $countryInfo[0]["name"];

			$overdayFlight = false;
			if(isset($planInfo->flight) && $planInfo->flight != false){
				$flightId = $planInfo->flightId;
				$this->smarty->assign("flightId", $flightId);

				$itineraryGenerate->flight = $planInfo->flight;
				$itineraryGenerate->flight->id = $planInfo->flightId;
				$itineraryGenerate->flight->depart = $planInfo->flight->depart;
				$itineraryGenerate->flight->breturn = $planInfo->flight->back;
				if($planInfo->flight->depart[0]->from->date != $planInfo->flight->depart[0]->to->date) //夜宿機上
					$overdayFlight = true;

				$itineraryGenerate->flightTraffic = new StdClass;
			}

			if(isset($planInfo->planList) && $planInfo->planList != false){
				foreach ($planInfo->planList as $key => $plan) { //列出每天行程
					$tmpDayPlan = new StdClass;
					$tmpDayPlan->startTime = "08:00";
					$tmpDayPlan->placeList = $plan->cityList;

					if($key == 0 && $overdayFlight == true)
						$tmpDayPlan->overdayFlight = true;

					if(isset($plan->hotel) && $plan->hotel != null){
						$tmpDayPlan->hotelInfo["duration"] = 0;
						$tmpDayPlan->hotelInfo["id"] = "";
						$tmpDayPlan->hotelInfo["key"] = "1_".$plan->hotel->id;
						$tmpDayPlan->hotelInfo["pid"] = "1_".$plan->hotel->id;
						$tmpDayPlan->hotelInfo["name"] = $plan->hotel->name;
						$tmpDayPlan->hotelInfo["type"] = "hotel";

						$tmpDayPlan->hotelInfo["thumbnail"][0] = new StdClass;
						$tmpDayPlan->hotelInfo["thumbnail"][0]->img = $plan->hotel->thumbnail;
						$tmpDayPlan->hotelInfo["guideId"] = $plan->hotel->nowGuide->id;
						$tmpDayPlan->hotelInfo["guideList"] = $plan->hotel->guideList;
					}
					else
						$tmpDayPlan->hotelInfo = false;

					$tmpDayPlan->hotelcity = new StdClass;
					if(isset($plan->hotelcity) && $plan->hotelcity != null)
						$tmpDayPlan->hotelcity = $plan->hotelcity;

					if(isset($plan->itemList) && $plan->itemList){
						foreach ($plan->itemList as $itemkey => $itemInfo) { //列出行程item
							if(isset($itemInfo->mode) && $itemInfo->mode != ""){
								if($itemInfo->mode != 4) //元組件
								{
									$tmpPoiId = $itemInfo->id;

									if($itemInfo->category != "0")
										$itemInfo->pid = "1_".$tmpPoiId;
									else
										$itemInfo->pid = "0_".$tmpPoiId;

									$itemInfo->guideId = $itemInfo->nowGuide;
									$itemInfo->type = "poi";
									$itemInfo->name = specialStringReplace($itemInfo->name);

									$itemInfo->startTime = $itemInfo->starttime;
									$itemInfo->endTime = $itemInfo->endtime;

									if(isset($itemInfo->guideList)){
										for($g = 0; $g < count($itemInfo->guideList); $g++){
											if($itemInfo->guideList[$g]->id == $itemInfo->nowGuide)
												$itemInfo->nowGuideInfo = $itemInfo->guideList[$g];
											$itemInfo->guideList[$g]->brief = specialStringReplace($itemInfo->guideList[$g]->brief);
										}
									}

									if(isset($itemInfo->location->latitude) && isset($itemInfo->location->longitude)){
										$itemInfo->latitude = $itemInfo->location->latitude;
										$itemInfo->longitude = $itemInfo->location->longitude;
									}
									else{
										$itemInfo->latitude = "";
										$itemInfo->longitude = "";
									}
								}
								else{
									$itemInfo->id = "custom-".$key."-".$itemkey;
									$itemInfo->pid = "custom-".$key."-".$itemkey;
									$itemInfo->key = "custom-".$key."-".$itemkey;
									$itemInfo->type = "custom";
									$itemInfo->name = $itemInfo->title;
									$itemInfo->place = $itemInfo->addr;

									if(strlen($itemInfo->astarttime) < 4)
										$itemInfo->astarttime = "0".$itemInfo->astarttime;
									$tmpDate = DateTime::createFromFormat("Hi", $itemInfo->astarttime);
									$tmpDate = $tmpDate->format("H:i");
									$itemInfo->startTime = $tmpDate;

									if(strlen($itemInfo->aendtime) < 4)
										$itemInfo->aendtime = "0".$itemInfo->aendtime;
									$tmpDate = DateTime::createFromFormat("Hi", $itemInfo->aendtime);
									$tmpDate = $tmpDate->format("H:i");
									$itemInfo->endTime = $tmpDate;

									if($itemInfo->startTime != $itemInfo->endTime)
										$itemInfo->duration = (strtotime($itemInfo->endTime) - strtotime($itemInfo->startTime))/60;

									if($itemInfo->dispatch == 1)
										$itemInfo->car = "1";
									else
										$itemInfo->car = "0";

									$itemInfo->contactName = $itemInfo->contact;
									$itemInfo->contactPhone = $itemInfo->phone;
									$itemInfo->memo = $itemInfo->desc;

									if($itemInfo->type == "2")
										$itemInfo->spots = "1";
									else
										$itemInfo->spots = "0";

									$itemInfo->latitude = "";
									$itemInfo->longitude = "";
									$itemInfo->latitudeLeave = "";
									$itemInfo->longitudeLeave = "";
									$itemInfo->guideId = "";
									$itemInfo->guideList = false;
									$itemInfo->thumbnail = "";

									$itemInfo->address = [];
									if(isset($itemInfo->off) && $itemInfo->off){
										for($c = 0; $c < count($itemInfo->off); $c++){
											$tmpArr = new stdclass;
											$tmpArr->latitude = "";
											$tmpArr->longitude = "";
											// $tmpArr->address = $itemInfo->off[$c]->onaddr;
											$tmpArr->address = $itemInfo->off[$c]->addr;

											if($itemInfo->car == "1"){
												// if(strlen($itemInfo->off[$c]->offdate) < 4)
												if(strlen($itemInfo->off[$c]->date) < 4)
													// $itemInfo->off[$c]->offdate = "0".$itemInfo->off[$c]->offdate;
													$itemInfo->off[$c]->date = "0".$itemInfo->off[$c]->date;
												// $tmpDate = DateTime::createFromFormat("Hi", $itemInfo->off[$c]->offdate);
												$tmpDate = DateTime::createFromFormat("Hi", $itemInfo->off[$c]->date);
												$tmpDate = $tmpDate->format("H:i");
												$tmpArr->carTime = $tmpDate;
											}
											$itemInfo->address[] = $tmpArr;
										}
									}
								}
							}
							else
								unset($plan->itemList[$itemkey]);
						}
						$tmpDayPlan->itemList = $plan->itemList;
					}
					$itineraryGenerate->scheduleList[] = $tmpDayPlan;
				}
			}
			$itineraryObj = json_encode($itineraryGenerate);

			if(isset($planInfo->cbmin))
				$planData->cbmin = $planInfo->cbmin;
		}

		$this->smarty->assign("isOldPlan", false);
		$this->smarty->assign("template", $template);
		$this->smarty->assign("status", $status);
		$this->smarty->assign("type", $type);
		$this->smarty->assign("itineraryObj", $itineraryObj);
		$this->smarty->assign("planData", $planData);

		//$this->smarty->assign("continent", $continent);
		//$this->smarty->assign("country", $country);
		$this->smarty->assign("areaID", $areaID);
		$this->smarty->assign("areaName", $areaName);
		$this->smarty->assign("planId", $planId);

		$poiCategory = $this->Place_model->getPoiCategory(false);

		$this->smarty->assign("poiCategory", $poiCategory);

		$this->smarty->assign("cityFlag", $cityFlag);
		$this->smarty->assign("itineraryTitle", $itineraryTitle);
		$this->smarty->assign("itineraryDepartDate", $itineraryDepartDate);
		$this->smarty->assign("destinationList", $destinationList);
		$this->smarty->assign("destinationJson", $destinationJson);

		$this->smarty->assign("defaultDay", $defaultDay);
		$this->smarty->assign("searchBarSwitch", false);
		$this->smarty->assign("ContentPage", "planner/combinition_edit.tpl");
        $this->smarty->view('master.tpl');
	}

	//replace savePlanner/old
	public function oldSavePlanner($planId, $type)
	{
		global $config, $userSessionData;

		$filter["id"] = $planId;
		$filter["userid"] = $userSessionData["userId"];
		$filter['Entrance'] = 1;	//1:B2B 2:ERP
		$planData = getRequestV2("v2_OldPlanInfo", $filter);

		if(empty($planData->Data))
		{
			echo '無行程資料，五秒後導入行程管理列表頁。';
			echo '<script type="text/javascript">setTimeout(function(){ window.location.href = "' . $GLOBALS["site_url"] . 'user/scheduleList"; }, 5000);</script>';
			exit;
		}
		else
			$plan = $planData->Data->OldPlannerData;

		//set save type
		if($type == "new")
		{
			$data["planId"] = null;
			$data["CBMId"] = null;
			$data["bntid"] = 0;
		}
		elseif($type == "edit" || $type == "confirm")
		{
			$data["planId"] = $plan->planId;
			$data["CBMId"] = null;
			$data["bntid"] = 0;
		}
		elseif($type == "overwrite")
		{
			$data["planId"] = $plan->planId;
			$data["CBMId"] = $plan->CBMId;
			$data["bntid"] = 0;
		}
		elseif($type == "send" || $type == "ajaxSend")
		{
			$data["planId"] = $plan->planId;
			$data["CBMId"] = is_null($plan->CBMId) ? "0" : $plan->CBMId;
			$data["bntid"] = 1;
		}

		$data["PCMId"] = $plan->PCMId;
		$data["from"] = $userSessionData["refer"];
		$data["Entrance"] = 1;
		$data["groupid"] = $plan->groupid;
		$data["name"] = $plan->name;
		$data["lineId"] = $plan->lineId;
		$data["dlineId"] = $plan->dlineId;
		$data["dayNum"] = $plan->dayNum;
		$data["userid"] = $userSessionData["userId"];
		$data["flightid"] = $plan->flight[0]->id;

		$data["flightmemo"] = new StdClass;
		$data["flightmemo"]->type = "FLIGHT";
		$data["flightmemo"]->desc = isset($_POST["flightMemo"]) ? $this->input->post("flightMemo") : $plan->flightmemo;

		$data["cbmin"] = new StdClass;
		if(!empty($plan->cbmin)){
			$data["cbmin"]->no			= isset($_POST["no"])				? $this->input->post('no') 				: $plan->cbmin->no;
			$data["cbmin"]->name 		= isset($_POST["planName"]) 		? $this->input->post('planName') 		: $plan->cbmin->name;
			$data["cbmin"]->dayNum 		= isset($_POST["days"]) 			? (int)$this->input->post('days') 		: $plan->cbmin->dayNum;
			$data["cbmin"]->people 		= isset($_POST["people"]) 			? (int)$this->input->post('people') 	: $plan->cbmin->people;
			$data["cbmin"]->budgetMin 	= isset($_POST["budgetMin"]) 		? (int)$this->input->post('budgetMin') 	: $plan->cbmin->budgetMin;
			$data["cbmin"]->budgetMax 	= isset($_POST["budgetMax"]) 		? (int)$this->input->post('budgetMax') 	: $plan->cbmin->budgetMax;
			$data["cbmin"]->Src 		= isset($_POST["purpose"]) 			? $this->input->post('purpose') 		: $plan->cbmin->Src;
			$data["cbmin"]->companyName = isset($_POST["companyName"]) 		? $this->input->post('companyName') 	: $plan->cbmin->companyName;
			$data["cbmin"]->customerId 	= isset($_POST["customerId"]) 		? $this->input->post('customerId') 		: $plan->cbmin->customerId;
			$data["cbmin"]->personId 	= isset($_POST["personId"]) 		? $this->input->post('personId') 		: $plan->cbmin->personId;
			$data["cbmin"]->contactName = isset($_POST["contactName"]) 		? $this->input->post('contactName')		: $plan->cbmin->contactName;
			$data["cbmin"]->areacode 	= isset($_POST["areacode"]) 		? $this->input->post('areacode') 		: $plan->cbmin->areacode;
			$data["cbmin"]->phone 		= isset($_POST["contactPhone"]) 	? $this->input->post('contactPhone') 	: $plan->cbmin->phone;
			$data["cbmin"]->phoneExt 	= isset($_POST["contactPhoneExt"]) 	? $this->input->post('contactPhoneExt') : $plan->cbmin->phoneExt;
			$data["cbmin"]->mobile 		= isset($_POST["contactMobile"]) 	? preg_replace('/[^0-9]/', '', $this->input->post('contactMobile')) : preg_replace('/[^0-9]/', '', $plan->cbmin->mobile);
			$data["cbmin"]->email 		= isset($_POST["contactEmail"]) 	? $this->input->post('contactEmail') 	: $plan->cbmin->email;
			$data["cbmin"]->address 	= isset($_POST["contactAddress"]) 	? $this->input->post('contactAddress') 	: $plan->cbmin->address;
			$data["cbmin"]->salesId 	= isset($_POST["salesId"]) 			? $this->input->post('salesId') 		: $plan->cbmin->salesId;
			$data["cbmin"]->TpId 		= isset($_POST["TpId"]) 			? $this->input->post('TpId') 			: $plan->cbmin->TpId;
			$data["cbmin"]->TpUnit 		= isset($_POST["TpUnit"]) 			? $this->input->post('TpUnit')		 	: $plan->cbmin->TpUnit;
			$data["cbmin"]->TpName 		= isset($_POST["TpName"]) 			? $this->input->post('TpName') 			: $plan->cbmin->TpName;
			$data["cbmin"]->remark 		= isset($_POST["remark"]) 			? $this->input->post('remark') 			: $plan->cbmin->remark;

			//fill self-info if userType is TP
			if($userSessionData["userInfo"]->userType == "TP")
            {
                $data["cbmin"]->TpId = $plan->cbmin->TpId;
                $data["cbmin"]->TpName = $plan->cbmin->TpName;
                $data["cbmin"]->TpUnit = $plan->cbmin->TpUnit;
            }

			if(isset($_POST["costInclude"]))
				$data["cbmin"]->costInclude = $this->input->post('costInclude') == "checked" ? true : false;
			else
				$data["cbmin"]->costInclude = $plan->cbmin->costInclude;

			if(isset($_POST["requiredDepartDate"]))
				$date = new DateTime($this->input->post('requiredDepartDate'));
			else
				$date = new DateTime("now");
			$data["cbmin"]->departDate = $date->format("Ymd");

			$data["cbmin"]->SPMList = [];
			if(isset($_POST["SPMList"]))
			{
				$SPMList = empty($this->input->post('SPMList')) ? [] : json_decode($this->input->post('SPMList'));
				foreach($SPMList as $SPM)
				{
					if($SPM->SPMId != "")
					{
						$spmData = new StdClass;
						$spmData->StfnType = $SPM->StfnType;
						$spmData->SPMId = $SPM->SPMId;
						$data["cbmin"]->SPMList[] = $spmData;
					}
				}
			}
			else
				$data["cbmin"]->SPMList = $plan->cbmin->SPMList;

			$data["cbmin"]->PSMList = [];
			if(isset($_POST["PSMList"]))
			{
				$PSMList = empty($this->input->post('PSMList')) ? [] : json_decode($this->input->post('PSMList'));
				foreach($PSMList as $PSM)
				{
					if($PSM->PSMId != "")
					{
						$psmData = new StdClass;
						$psmData->StfnType = $PSM->StfnType;
						$psmData->PSMId = $PSM->PSMId;
						$data["cbmin"]->PSMList[] = $psmData;
					}
				}
			}
			else
				$data["cbmin"]->PSMList = $plan->cbmin->PSMList;
		}

		$data["planList"] = [];
		if(isset($_POST["planInfo"]))
		{
			foreach ($this->input->post('planInfo') as $day => $planInfo)
			{
				$pdata = new StdClass;
				$pdata->day = $day + 1;
				$pdata->title = $planInfo['title'];
				$pdata->specialTitle = '';
				$pdata->information = $planInfo['information'];
				$pdata->spot = [
					'type' => 'VIEW',
					'spotName' => null
				];
				$meals = [];
				$sort = 0;
				if(isset($planInfo['meal']) && $planInfo['meal'] != false)
				{
					foreach ($planInfo['meal'] as $mealInfo) {
						$meals[$sort]['type'] = 'MEAT';
						$meals[$sort]['mealSort'] = $sort + 1;
						$meals[$sort]['mealName'] = $mealInfo;
						$sort = $sort + 1;
					}
				}
				$pdata->meal = $meals;

				$hotel = []; // not array? only one hotel allowed in such case
				$hotel['type'] = 'HOTEL';
				$hotel['hotelContent'] = $planInfo['hotel']['hotelContent'];
				$pdata->hotel = $hotel;

				$data["planList"][] = $pdata;
			}
		}
		else
		{
			foreach ($plan->planList as $day => $planInfo)
			{
				$pdata = new StdClass;
				$pdata = $planInfo;
				$data["planList"][] = $pdata;
			}
		}

		$updatePlanData = postJSONRequest("v2_UpdateUserOldPlanInfo", $data);

		if($type == "confirm" || $type == "ajaxSend")
		{
			if(isset($updatePlanData->rDesc) && $updatePlanData->rDesc != "")
				echo json_encode(['status' => 'FAIL', 'msg' => $updatePlanData->rDesc]);
			else
				echo json_encode(['status' => 'OK', 'msg' => $updatePlanData->rDesc]);
		}
		else
		{
			if(isset($updatePlanData) && isset($updatePlanData->Data->planId))
			$planId = $updatePlanData->Data->planId;
			else
			{
				if(isset($updatePlanData->rDesc) && $updatePlanData->rDesc != "")
					$errorMsg = "ERROR: ".$updatePlanData->rDesc;
				else
					$errorMsg = "API ERROR";

				$this->session->set_flashdata('messages', $errorMsg);
				$this->load->library('user_agent');
				redirect($this->agent->referrer());
			}

			if($type == "overwrite" || $type == "new")
				redirect($GLOBALS["site_url"]."planner/oldEdit/".$planId."/edit");
			else
				redirect($GLOBALS["site_url"]."planner/old/".$planId);
		}
	}

	//replace infoForm/new
	public function saveCombinition($planId, $type)
	{
		global $config, $userSessionData;

		$planData = $this->apiGetCombinationPlanInfo($planId);
		if(empty($planData->Data))
		{
			echo '無行程資料，五秒後導入行程管理列表頁。';
			echo '<script type="text/javascript">setTimeout(function(){ window.location.href = "' . $GLOBALS["site_url"] . 'user/scheduleList"; }, 5000);</script>';
			exit;
		}

		$plan = $planData->Data->PlannerData;
	
		if($type == "new")
		{
			$data["planId"] = null;
			$data["CBMId"] = null;
			$data["bntid"] = 0;
		}
		elseif($type == "overwrite")
		{
			$data["planId"] = $plan->planId;
			$data["CBMId"] = $plan->CBMId;
			$data["bntid"] = 0;
		}
		elseif($type == "send" || $type == "saveRequired")
		{
			$data["planId"] = $plan->planId;
			$data["CBMId"] = is_null($plan->CBMId) ? "0" : $plan->CBMId;
			$data["bntid"] = 1;
		}

		$data["PCMId"] = $plan->PCMId;
		$data["from"] = $userSessionData["refer"];
		$data["Entrance"] = 1;
		$data["name"] = $plan->name;
		$data["dayNum"] = $plan->dayNum;
		$data["userid"] = $userSessionData["userId"];
		$data["departDate"] = $plan->departDate;
		$data["flightId"] = $plan->flightId;

		//flight
		$data["flight"] = new StdClass;
		if(isset($plan->flight))
		{
			$data["flight"]->depart = new StdClass;
			$data["flight"]->depart->routeId = $plan->flight->depart[0]->distance->routId;
			$data["flight"]->depart->value = $plan->flight->depart[0]->distance->value;
			$data["flight"]->depart->time = $plan->flight->depart[0]->distance->time;
			$data["flight"]->back = new StdClass;
			$data["flight"]->back->routeId = $plan->flight->back[0]->distance->routId;
			$data["flight"]->back->value = $plan->flight->back[0]->distance->value;
			$data["flight"]->back->time = $plan->flight->back[0]->distance->time;
		}

		//area
		$data["area"] = [];
		$area = new StdClass;
		$area->areaId = $plan->areaId;
		$data["area"][0] = $area;

		//planList
		$data["planList"] = [];
		if(isset($plan->planList))
		{
			foreach($plan->planList as $key=>$dayPlan)
			{
				$planItem = new StdClass;
				$planItem->day = $key+1;
				//hotel
				$planItem->hotel = new StdClass;
				if(!is_null($dayPlan->hotel))
				{
					$planItem->hotel->id = $dayPlan->hotel->id;
					$planItem->hotel->foiid = $dayPlan->hotel->nowGuide->id;
				}

				if(!is_null($dayPlan->hotelcity))
					$planItem->hotelcity = $dayPlan->hotelcity;

				//itemList
				$planItem->itemList = [];
				foreach($dayPlan->itemList as $itemKey=>$item)
				{
					$itemData = new StdClass;
					$itemData->id = $item->id;
					$itemData->foiid = ($item->category == 0 || $item->mode == 4) ? 0 : $item->nowGuide;
					$itemData->sort = $itemKey+1;
					$itemData->time = $item->duration;
					$itemData->mode = $item->mode;
					$itemData->starttime = $item->starttime;
					$itemData->endtime = $item->endtime;
					$itemData->title = $item->title;
					$itemData->addr = $item->addr;
					$itemData->astarttime = $item->astarttime;
					$itemData->aendtime = $item->aendtime;
					$itemData->dispatch = $item->dispatch;
					$itemData->contact = $item->contact;
					$itemData->phone = $item->phone;
					$itemData->desc = $item->desc;
					$itemData->type = $item->type;
					$itemData->off = $item->off;

					$planItem->itemList[] = $itemData;
				}
				$data["planList"][] = $planItem;
			}
		}

		//cbmin
		$data["cbmin"] = new StdClass;
		if(isset($plan->cbmin))
		{
			//需求表單
			$data["cbmin"] = new StdClass;
			$data["cbmin"]->no			= ($type == "saveRequired" && isset($_POST["no"]))				? $this->input->post('no') 				: $plan->cbmin->no;
			$data["cbmin"]->name 		= ($type == "saveRequired" && isset($_POST["planName"])) 		? $this->input->post('planName') 		: $plan->cbmin->name;
			$data["cbmin"]->dayNum 		= ($type == "saveRequired" && isset($_POST["days"])) 			? (int)$this->input->post('days') 		: $plan->cbmin->dayNum;
			$data["cbmin"]->people 		= ($type == "saveRequired" && isset($_POST["people"])) 			? (int)$this->input->post('people') 	: $plan->cbmin->people;
			$data["cbmin"]->budgetMin 	= ($type == "saveRequired" && isset($_POST["budgetMin"])) 		? (int)$this->input->post('budgetMin') 	: $plan->cbmin->budgetMin;
			$data["cbmin"]->budgetMax 	= ($type == "saveRequired" && isset($_POST["budgetMax"])) 		? (int)$this->input->post('budgetMax') 	: $plan->cbmin->budgetMax;
			$data["cbmin"]->Src 		= ($type == "saveRequired" && isset($_POST["purpose"])) 		? $this->input->post('purpose') 		: $plan->cbmin->Src;
			$data["cbmin"]->companyName = ($type == "saveRequired" && isset($_POST["companyName"])) 	? $this->input->post('companyName') 	: $plan->cbmin->companyName;
			$data["cbmin"]->customerId 	= ($type == "saveRequired" && isset($_POST["customerId"])) 		? $this->input->post('customerId') 		: $plan->cbmin->customerId;
			$data["cbmin"]->personId 	= ($type == "saveRequired" && isset($_POST["personId"])) 		? $this->input->post('personId') 		: $plan->cbmin->personId;
			$data["cbmin"]->contactName = ($type == "saveRequired" && isset($_POST["contactName"])) 	? $this->input->post('contactName')		: $plan->cbmin->contactName;
			$data["cbmin"]->areacode 	= ($type == "saveRequired" && isset($_POST["areacode"])) 		? $this->input->post('areacode') 		: $plan->cbmin->areacode;
			$data["cbmin"]->phone 		= ($type == "saveRequired" && isset($_POST["contactPhone"])) 	? $this->input->post('contactPhone') 	: $plan->cbmin->phone;
			$data["cbmin"]->phoneExt 	= ($type == "saveRequired" && isset($_POST["contactPhoneExt"])) ? $this->input->post('contactPhoneExt') : $plan->cbmin->phoneExt;
			$data["cbmin"]->mobile 		= ($type == "saveRequired" && isset($_POST["contactMobile"])) 	? preg_replace('/[^0-9]/', '', $this->input->post('contactMobile')) : preg_replace('/[^0-9]/', '', $plan->cbmin->mobile);
			$data["cbmin"]->email 		= ($type == "saveRequired" && isset($_POST["contactEmail"])) 	? $this->input->post('contactEmail') 	: $plan->cbmin->email;
			$data["cbmin"]->address 	= ($type == "saveRequired" && isset($_POST["contactAddress"])) 	? $this->input->post('contactAddress') 	: $plan->cbmin->address;
			$data["cbmin"]->salesId 	= ($type == "saveRequired" && isset($_POST["salesId"])) 		? $this->input->post('salesId') 		: $plan->cbmin->salesId;
			$data["cbmin"]->TpId 		= ($type == "saveRequired" && isset($_POST["TpId"])) 			? $this->input->post('TpId') 			: $plan->cbmin->TpId;
			$data["cbmin"]->TpUnit 		= ($type == "saveRequired" && isset($_POST["TpUnit"])) 			? $this->input->post('TpUnit')		 	: $plan->cbmin->TpUnit;
			$data["cbmin"]->TpName 		= ($type == "saveRequired" && isset($_POST["TpName"])) 			? $this->input->post('TpName') 			: $plan->cbmin->TpName;
			$data["cbmin"]->remark 		= ($type == "saveRequired" && isset($_POST["remark"])) 			? $this->input->post('remark') 			: $plan->cbmin->remark;
			$data["cbmin"]->salesId 	= $plan->cbmin->salesId;
			$data["cbmin"]->salesName 	= $plan->cbmin->salesName;
			$data["cbmin"]->salesPhone 	= $plan->cbmin->salesPhone;
			$data["cbmin"]->salesMobile = preg_replace('/[^0-9]/', '', $plan->cbmin->salesMobile);
			$data["cbmin"]->salesEmail 	= $plan->cbmin->salesEmail;

			if($type == "saveRequired" && isset($_POST["costInclude"]))
				$data["cbmin"]->costInclude = $this->input->post('costInclude') == "checked" ? true : false;
			else
				$data["cbmin"]->costInclude = $plan->cbmin->costInclude;

			if($type == "saveRequired" && isset($_POST["requiredDepartDate"]))
			{
				$date = new DateTime($this->input->post('requiredDepartDate'));
				$data["cbmin"]->departDate = $date->format("Ymd");
			}
			else
				$data["cbmin"]->departDate = $plan->cbmin->departDate;;

			$data["cbmin"]->SPMList = [];
			if($type == "saveRequired" && isset($_POST["SPMList"]))
			{
				$SPMList = empty($this->input->post('SPMList')) ? [] : json_decode($this->input->post('SPMList'));
				foreach($SPMList as $SPM)
				{
					if($SPM->SPMId != "")
					{
						$spmData = new StdClass;
						$spmData->StfnType = $SPM->StfnType;
						$spmData->SPMId = $SPM->SPMId;
						$data["cbmin"]->SPMList[] = $spmData;
					}
				}
			}
			else
				$data["cbmin"]->SPMList = $plan->cbmin->SPMList;

			$data["cbmin"]->PSMList = [];
			if($type == "saveRequired" && isset($_POST["PSMList"]))
			{
				$PSMList = empty($this->input->post('PSMList')) ? [] : json_decode($this->input->post('PSMList'));
				foreach($PSMList as $PSM)
				{
					if($PSM->PSMId != "")
					{
						$psmData = new StdClass;
						$psmData->StfnType = $PSM->StfnType;
						$psmData->PSMId = $PSM->PSMId;
						$data["cbmin"]->PSMList[] = $psmData;
					}
				}
			}
			else
				$data["cbmin"]->PSMList = $plan->cbmin->PSMList;
		}
	
		$updateRet = postJSONRequest("v2_UpdateUserPlanInfo", $data);

		if(isset($updateRet) && isset($updateRet->Data->planId))
			$newPlanId = $updateRet->Data->planId;
		else
		{
			if(isset($updateRet->rDesc) && $updateRet->rDesc != "")
				$errorMsg = "ERROR: ".$updateRet->rDesc;
			else
				$errorMsg = "API ERROR";

			$this->session->set_flashdata('messages', $errorMsg);
			$this->load->library('user_agent');
			redirect($this->agent->referrer());
		}

		if($type == "send" || $type == "saveRequired")
			redirect($GLOBALS["site_url"]."planner/getCombinition/".$newPlanId);
		else
			redirect($GLOBALS["site_url"]."planner/editCombinition/".$newPlanId."/edit");
	}

	//replace infoForm
	public function saveformCombinition($planId, $type)
	{
		global $config, $userSessionData;

		if($type != "new")
		{
			$planData = $this->apiGetCombinationPlanInfo($planId);
			if(empty($planData->Data))
			{
				echo '無行程資料，五秒後導入行程管理列表頁。';
				echo '<script type="text/javascript">setTimeout(function(){ window.location.href = "' . $GLOBALS["site_url"] . 'user/scheduleList"; }, 5000);</script>';
				exit;
			}
			$plan = $planData->Data->PlannerData;
		}

		//set save type
		if($type == "new")
		{
			$data["planId"] = null;
			$data["CBMId"] = null;
			$data["bntid"] = 0;
		}
		if($type == "edit" || $type == "confirm")
		{
			$data["planId"] = $plan->planId;
			$data["CBMId"] = null;
			$data["bntid"] = 0;
		}
		elseif($type == "send")
		{
			$data["planId"] = $plan->planId;
			$data["CBMId"] = is_null($plan->CBMId) ? "0" : $plan->CBMId;
			$data["bntid"] = 1;
		}
		$data["PCMId"] = $type == "new" ? null : $plan->PCMId;
		$data["Entrance"] = 1;

		$itineraryObject = $this->input->post("itineraryObject");
		$areaID = $this->input->post("areaID");
		$departDate = $this->input->post("departDate");
		$flightId = $this->input->post("flightId");

		if(!isset($departDate) || $departDate == false)
			$departDate = date("Y-m-d");

		if($itineraryObject != ""){
			$itineraryInfo = json_decode($itineraryObject);

			$data["from"] = $userSessionData["refer"];
			$data["name"] = $itineraryInfo->title;
			$data["dayNum"] = $itineraryInfo->days;
			$data["userid"] = $userSessionData["userId"];
			$data["departDate"] = date("Ymd", strtotime($departDate));

			if(isset($itineraryInfo->flight->id) && $itineraryInfo->flight->id != false)
				$data["flightId"] = $itineraryInfo->flight->id;
			else //如果是複製有可能已經有則要填入舊的flightId
				$data["flightId"] = $flightId;

			$data["area"] = [];
			$data["area"][0] = new StdClass;
			$data["area"][0]->areaId = $areaID;
			$data["planList"] = "";

			if(isset($itineraryInfo->flightTraffic) && $itineraryInfo->flightTraffic){
				//有機場交通要塞到api中
				$data["flight"]["depart"] = [];
				if(isset($itineraryInfo->flightTraffic->depart) && $itineraryInfo->flightTraffic->depart){
					$data["flight"]["depart"]["routeId"] = (int)$itineraryInfo->flightTraffic->depart->trafficInfo->routes;
					$data["flight"]["depart"]["value"] = (int)$itineraryInfo->flightTraffic->depart->trafficInfo->distance;
					$data["flight"]["depart"]["time"] = (int)$itineraryInfo->flightTraffic->depart->trafficInfo->duration;
				}
				else
				{
					$data["flight"]["depart"]["routeId"] = 0;
					$data["flight"]["depart"]["value"] = 0;
					$data["flight"]["depart"]["time"] = 0;
				}

				$data["flight"]["back"] = [];
				if(isset($itineraryInfo->flightTraffic->return) && $itineraryInfo->flightTraffic->return){
					$data["flight"]["back"]["routeId"] = (int)$itineraryInfo->flightTraffic->return->trafficInfo->routes;
					$data["flight"]["back"]["value"] = (int)$itineraryInfo->flightTraffic->return->trafficInfo->distance;
					$data["flight"]["back"]["time"] = (int)$itineraryInfo->flightTraffic->return->trafficInfo->duration;
				}
				else
				{
					$data["flight"]["back"]["routeId"] = 0;
					$data["flight"]["back"]["value"] = 0;
					$data["flight"]["back"]["time"] = 0;
				}
			}

			if(isset($itineraryInfo->scheduleList) && $itineraryInfo->scheduleList){
				$nowDay = 1;
				$scheduleData = [];
				foreach($itineraryInfo->scheduleList as $scheduleInfo){
					$tmpSchedule = new stdclass;
					$tmpSchedule->day = $nowDay;
					$tmpSchedule->hotel = new stdclass;

					if(isset($scheduleInfo->hotelInfo->pid) && $scheduleInfo->hotelInfo->pid != false){
						$tmp = explode("_", $scheduleInfo->hotelInfo->pid);
						$tmpSchedule->hotel->id = $tmp[1];
						$tmpSchedule->hotel->foiId = $scheduleInfo->hotelInfo->guideId;
					}
					else{
						$tmpSchedule->hotel->id = "";
						$tmpSchedule->hotel->foiId = "";
					}

					$tmpSchedule->hotelcity = new StdClass;
					if(isset($scheduleInfo->hotelcity) && !is_null($scheduleInfo->hotelcity))
					{
						if(isset($scheduleInfo->hotelcity->from))
						{
							$tmpSchedule->hotelcity->from = new StdClass;
							$tmpSchedule->hotelcity->from->cityID = $scheduleInfo->hotelcity->from->cityID;
							$tmpSchedule->hotelcity->from->cityName = $scheduleInfo->hotelcity->from->cityName;
						}
						if(isset($scheduleInfo->hotelcity->to))
						{
							$tmpSchedule->hotelcity->to = new StdClass;
							$tmpSchedule->hotelcity->to->cityID = $scheduleInfo->hotelcity->to->cityID;
							$tmpSchedule->hotelcity->to->cityName = $scheduleInfo->hotelcity->to->cityName;
						}
					}

					$tmpSchedule->itemList = [];
					if(isset($scheduleInfo->itemList) && $scheduleInfo->itemList)
					{
						foreach($scheduleInfo->itemList as $key => $itemInfo)
						{
							if(isset($itemInfo->type) && $itemInfo->type != "")
							{
								$tmpItem["sort"] = $key+1;
								$tmpItem["time"] = $itemInfo->duration;
								$tmpItem["starttime"] = getTimeFormat($itemInfo->startTime);
								$tmpItem["endtime"] = getTimeFormat($itemInfo->endTime);
							
								if($itemInfo->type=="poi")
								{
									$tmp = explode("_", $itemInfo->pid);
									$tmpItem["id"] = $tmp[1]; //組件/元件id
								}
								else
								{
									$tmpItem["id"] = "";
									$tmpItem["foiid"] = "";
								}

								if($itemInfo->type=="poi" && $tmp[0] == 0) //組件
								{
									$tmpItem["mode"] = 2;
									$tmpItem["foiid"] = 0;
								}
								elseif($itemInfo->type=="poi" && $tmp[0] == 1) //元件
								{
									$tmpItem["mode"] = 1;
									if(isset($itemInfo->guideId))
										$tmpItem["foiid"] = $itemInfo->guideId; //玩法id
									else
									{
										if(isset($itemInfo->guideList) && count($itemInfo->guideList) > 0)
											$tmpItem["foiid"] = $itemInfo->guideList[0]->id;
										else
											$tmpItem["foiid"] = 0;
									}
								}

								//處理位置
								if(isset($itemInfo->latitude) && $itemInfo->latitude != false)
									$tmpItem["location"]["latitude"] = $itemInfo->latitude;
								else
									$tmpItem["location"]["latitude"] = 0;

								if(isset($itemInfo->longitude) && $itemInfo->longitude != false)
									$tmpItem["location"]["longitude"] = $itemInfo->longitude;
								else
									$tmpItem["location"]["longitude"] = 0;

								if($itemInfo->type=="custom")
								{
									$tmpItem["mode"] = 4;
									$tmpItem["foiid"] = 0;
									$tmpItem["title"] = $itemInfo->name; //自訂活動才需輸入
									$tmpItem["addr"] = $itemInfo->place;
									$tmpItem["astarttime"] = getTimeFormat($itemInfo->startTime);
									$tmpItem["aendtime"] = getTimeFormat($itemInfo->endTime);

									if($itemInfo->car == "0")
										$tmpItem["dispatch"] = 0;
									else
										$tmpItem["dispatch"] = 1;

									$tmpItem["contact"] = $itemInfo->contactName;
									$tmpItem["phone"] = $itemInfo->contactPhone;
									$tmpItem["desc"] = $itemInfo->memo;

									if($itemInfo->spots == "1")
										$tmpItem["type"] = "2";
									else
										$tmpItem["type"] = "1";

									$tmpItem["off"] = [];
									if(isset($itemInfo->address) && $itemInfo->address)
									{
										for($c = 0; $c < count($itemInfo->address); $c++)
										{
											$tmpItem["off"][$c] = new stdclass;
											$tmpItem['off'][$c]->sort = $c + 1;
											$tmpItem['off'][$c]->addr = '';
											$tmpItem['off'][$c]->date = '';

											// $tmpItem["off"][$c]->onaddr = "";
											// $tmpItem["off"][$c]->offaddr = "";
											// $tmpItem["off"][$c]->offdate = "";

											if($itemInfo->car == "0") //不須出車
											{
												if($itemInfo->spots == "1") // 不同點上下車
												{
													if(isset($itemInfo->address[$c]->address)) {
														// $tmpItem["off"][$c]->onaddr = $itemInfo->address[$c]->address;
														$tmpItem['off'][$c]->addr = $itemInfo->address[$c]->address;
													}
													if(isset($itemInfo->address[$c]->addressDrop)) {
														// $tmpItem["off"][$c]->offaddr = $itemInfo->address[$c]->addressDrop;
														$tmpItem["off"][$c]->addr = $itemInfo->address[$c]->addressDrop;
													}
												}
												else{
													if(isset($itemInfo->address[$c]->address)) {
														// $tmpItem["off"][$c]->onaddr = $itemInfo->address[$c]->address;
														$tmpItem["off"][$c]->addr = $itemInfo->address[$c]->address;
													}
													if(isset($itemInfo->address[$c]->address)) {
														// $tmpItem["off"][$c]->offaddr = $itemInfo->address[$c]->address;
														$tmpItem["off"][$c]->addr = $itemInfo->address[$c]->address;
													}
												}
											}
											else{
												if(isset($itemInfo->address[$c]->address)) {
													// $tmpItem["off"][$c]->onaddr = $itemInfo->address[$c]->address;
													$tmpItem["off"][$c]->addr = $itemInfo->address[$c]->address;
												}
												if(isset($itemInfo->address[$c]->carTime)) {
													// $tmpItem["off"][$c]->offdate = getTimeFormat($itemInfo->address[$c]->carTime);
													$tmpItem["off"][$c]->date = getTimeFormat($itemInfo->address[$c]->carTime);
												}
											}
										}
									}
								}
								else
								{
									$tmpItem["title"] = ""; //自訂活動才需輸入
									$tmpItem["addr"] = "";
									$tmpItem["astarttime"] = "";
									$tmpItem["aendtime"] = "";
									$tmpItem["dispatch"] = "";
									$tmpItem["contact"] = "";
									$tmpItem["phone"] = "";
									$tmpItem["desc"] = "";
									$tmpItem["type"] = "";
									$tmpItem["off"] = [];
									$tmpItem["off"][0] = new stdclass;
									$tmpItem['off'][0]->sort = 0;
									$tmpItem['off'][0]->addr = '';
									$tmpItem['off'][0]->date = '';
								}
								$tmpSchedule->itemList[] = $tmpItem;
							}
						}
					}
					$nowDay++;
					$scheduleData[] = $tmpSchedule;
				}
			}
			$data["planList"] = $scheduleData;

			//需求表單
			$data["cbmin"] = new StdClass;
			$data["cbmin"]->no			= isset($_POST["no"])				? $this->input->post('no') 				: ($type == "new" ? 0 : $plan->cbmin->no);
			$data["cbmin"]->name 		= isset($_POST["planName"]) 		? $this->input->post('planName') 		: ($type == "new" ? "自訂需求名稱" : $plan->cbmin->name);
			$data["cbmin"]->dayNum 		= isset($_POST["days"]) 			? (int)$this->input->post('days') 		: ($type == "new" ? 0 : $plan->cbmin->dayNum);
			$data["cbmin"]->people 		= isset($_POST["people"]) 			? (int)$this->input->post('people') 	: ($type == "new" ? 0 : $plan->cbmin->people);
			$data["cbmin"]->budgetMin 	= isset($_POST["budgetMin"]) 		? (int)$this->input->post('budgetMin') 	: ($type == "new" ? 0 : $plan->cbmin->budgetMin);
			$data["cbmin"]->budgetMax 	= isset($_POST["budgetMax"]) 		? (int)$this->input->post('budgetMax') 	: ($type == "new" ? 0 : $plan->cbmin->budgetMax);
			$data["cbmin"]->Src 		= isset($_POST["purpose"]) 			? $this->input->post('purpose') 		: ($type == "new" ? 1 : $plan->cbmin->Src);
			$data["cbmin"]->companyName = isset($_POST["companyName"]) 		? $this->input->post('companyName') 	: ($type == "new" ? "" : $plan->cbmin->companyName);
			$data["cbmin"]->customerId 	= isset($_POST["customerId"]) 		? $this->input->post('customerId') 		: ($type == "new" ? 0 : $plan->cbmin->customerId);
			$data["cbmin"]->personId 	= isset($_POST["personId"]) 		? $this->input->post('personId') 		: ($type == "new" ? 0 : $plan->cbmin->personId);
			$data["cbmin"]->contactName = isset($_POST["contactName"]) 		? $this->input->post('contactName')		: ($type == "new" ? "自訂姓名" : $plan->cbmin->contactName);
			$data["cbmin"]->areacode 	= isset($_POST["areacode"]) 		? $this->input->post('areacode') 		: ($type == "new" ? "" : $plan->cbmin->areacode);
			$data["cbmin"]->phone 		= isset($_POST["contactPhone"]) 	? $this->input->post('contactPhone') 	: ($type == "new" ? "0123456789" : $plan->cbmin->phone);
			$data["cbmin"]->phoneExt 	= isset($_POST["contactPhoneExt"]) 	? $this->input->post('contactPhoneExt') : ($type == "new" ? "" : $plan->cbmin->phoneExt);
			$data["cbmin"]->mobile 		= isset($_POST["contactMobile"]) 	? preg_replace('/[^0-9]/', '', $this->input->post('contactMobile')) : ($type == "new" ? "0900000000" : preg_replace('/[^0-9]/', '', $plan->cbmin->mobile));
			$data["cbmin"]->email 		= isset($_POST["contactEmail"]) 	? $this->input->post('contactEmail') 	: ($type == "new" ? "" : $plan->cbmin->email);
			$data["cbmin"]->address 	= isset($_POST["contactAddress"]) 	? $this->input->post('contactAddress') 	: ($type == "new" ? "" : $plan->cbmin->address);
			$data["cbmin"]->salesId 	= isset($_POST["salesId"]) 			? $this->input->post('salesId') 		: ($type == "new" ? 0 : $plan->cbmin->salesId);
			$data["cbmin"]->TpId 		= isset($_POST["TpId"]) 			? $this->input->post('TpId') 			: ($type == "new" ? "" : $plan->cbmin->TpId);
			$data["cbmin"]->TpUnit 		= isset($_POST["TpUnit"]) 			? $this->input->post('TpUnit')		 	: ($type == "new" ? "" : $plan->cbmin->TpUnit);
			$data["cbmin"]->TpName 		= isset($_POST["TpName"]) 			? $this->input->post('TpName') 			: ($type == "new" ? "" : $plan->cbmin->TpName);
			$data["cbmin"]->remark 		= isset($_POST["remark"]) 			? $this->input->post('remark') 			: ($type == "new" ? "" : $plan->cbmin->remark);

            //fill self-info if userType is TP
            if($userSessionData["userInfo"]->userType == "TP")
            {
                $data["cbmin"]->TpId = $this->input->post('TpId');
                $data["cbmin"]->TpName = $this->input->post('TpName');
                $data["cbmin"]->TpUnit = $this->input->post('TpUnit');
            }

			if(isset($_POST["costInclude"]))
				$data["cbmin"]->costInclude = $this->input->post('costInclude') == "checked" ? true : false;
			else
				$data["cbmin"]->costInclude = $type == "new" ? false : $plan->cbmin->costInclude;

			if(isset($_POST["requiredDepartDate"]))
				$date = new DateTime($this->input->post('requiredDepartDate'));
			else
				$date = new DateTime("now");
			$data["cbmin"]->departDate = $date->format("Ymd");

			$data["cbmin"]->SPMList = [];
			if(isset($_POST["SPMList"]))
			{
				$SPMList = empty($this->input->post('SPMList')) ? [] : json_decode($this->input->post('SPMList'));
				foreach($SPMList as $SPM)
				{
					if($SPM->SPMId != "")
					{
						$spmData = new StdClass;
						$spmData->StfnType = $SPM->StfnType;
						$spmData->SPMId = $SPM->SPMId;
						$data["cbmin"]->SPMList[] = $spmData;
					}
				}
			}
			else
				$data["cbmin"]->SPMList = $type == "new" ? [] : $plan->cbmin->SPMList;

			$data["cbmin"]->PSMList = [];
			if(isset($_POST["PSMList"]))
			{
				$PSMList = empty($this->input->post('PSMList')) ? [] : json_decode($this->input->post('PSMList'));
				foreach($PSMList as $PSM)
				{
					if($PSM->PSMId != "")
					{
						$psmData = new StdClass;
						$psmData->StfnType = $PSM->StfnType;
						$psmData->PSMId = $PSM->PSMId;
						$data["cbmin"]->PSMList[] = $psmData;
					}
				}
			}
			else
				$data["cbmin"]->PSMList = $type == "new" ? [] : $plan->cbmin->PSMList;

			$updateRet = postJSONRequest("v2_UpdateUserPlanInfo", $data);
	
			if($type == "confirm")
			{
				if(isset($updateRet->rDesc) && $updateRet->rDesc != "")
					echo json_encode(['status' => 'FAIL', 'msg' => $updateRet->rDesc]);
				else
					echo json_encode(['status' => 'OK', 'msg' => $updateRet->rDesc]);
			}
			else
			{
				if(isset($updateRet) && isset($updateRet->Data->planId))
				{
					$newPlanId = $updateRet->Data->planId;
					redirect($GLOBALS["site_url"]."planner/getCombinition/".$newPlanId);
				}
				else
				{
					if(isset($updateRet->rDesc) && $updateRet->rDesc != "")
						$errorMsg = "ERROR: ".$updateRet->rDesc;
					else
						$errorMsg = "API ERROR";

					$this->session->set_flashdata('messages', $errorMsg);
					$this->load->library('user_agent');
					redirect($this->agent->referrer());
				}
			}
		}
		else
			echo "Empty Data";
	}

	public function getTPSearchConditionList()
	{
		$response = getRequestV2('v2_TPSearchConditionList', []);

		if ($response->rCode === null) {
			echo json_encode($response->Data);
		} else {
			echo json_encode([]);
		}
	}

	public function getSelectTPList()
	{
	    global $userSessionData;

	    $response = getRequestV2('v2_SelectTPList', [
			'stfnId' => (empty($this->input->get('stfnId')) ? null : $this->input->get('stfnId')), //起號(員工編號)
			'compId' => (empty($this->input->get('compId')) ? null : $this->input->get('compId')), //公司代碼
			'stfnjobId' => (empty($this->input->get('stfnjobId')) ? 31 : $this->input->get('stfnjobId')), //*工作代碼
			'cname' => (empty($this->input->get('cname')) ? null : $this->input->get('cname')), //員工姓名
			'sname' => (empty($this->input->get('sname')) ? null : $this->input->get('sname')), //C中文 E英文
			'prof' => (empty($this->input->get('prof')) ? null : $this->input->get('prof')), //單位
			'team' => (empty($this->input->get('team')) ? null : $this->input->get('team')), //組別
			'order' => (empty($this->input->get('order')) ? 0 : $this->input->get('order')), //順序(0:員編1:姓名2:單位3:職稱4:單位組5:單位職稱)
			'offset' => (empty($this->input->get('offset')) ? null : $this->input->get('offset')), //*起始筆數
			'max' => (empty($this->input->get('max')) ? null : $this->input->get('max')) //*一頁幾筆
	    ]);

		if ($response->rCode === null && !empty($response->Data)) {
			echo json_encode($response->Data);
		} else {
			echo json_encode([
				'Total' => 0,
				'IshlpList' => []
			]);
		}
	}

	public function getTPLogList()
	{
		global $userSessionData;

		$response = getRequestV2('v2_TPLogList', [
			'userid' => $userSessionData['userId'],
			'id' => $this->input->get('id'),
			'CBMId' => $this->input->get('CBMId')
		]);

		if ($response->rCode === null && !empty($response->Data)) {
			$payload = $response->Data;
			//正規
			if (empty($payload->OldTPList)) {
				$payload->OldTPList = [];
			}
			echo json_encode($payload);
		} else {
			echo json_encode([]);
		}
	}

	public function replacePlanTP()
	{
		global $userSessionData;

		$response = postJSONRequest('v2_ReplacePlanTP', [
			'planId' => $this->input->post('planId'),
			'CBMId' => $this->input->post('CBMId'),
			'TpId' => $this->input->post('TpId'),
			'TpUnit' => $this->input->post('TpUnit'),
			'userid' => $userSessionData['userId']
		]);

		echo json_encode($response);
	}

	public function sendPlanToConfirm($planId)
	{
		global $userSessionData;

		$sendTo = $this->input->get("sendTo");
		if($sendTo == "agent")
			$type = "v2_SendPlanToCustomer";
		elseif($sendTo == "sales")
			$type = "v2_SendPlanToSales";
		elseif($sendTo == "franchisee")
			$type = "v2_SendPlanToFranchisee";

		$filter['userId'] = $userSessionData["userId"];
		$filter['planId'] = $planId;
		$response = postJSONRequest($type, $filter);

		if ($response->rCode === null)
		{
			$result['status'] = "OK";
			$result['msg'] = null;
		}
		else
		{
			$result['status'] = "ERROR";
			$result['msg'] = $response->rDesc;
		}

		echo json_encode($result);
	}

	public function getRequireContactPersonList()
	{
		global $userSessionData;

		$response = getRequestV2('v2_SelectContactPersonList', [
			'userid' => $userSessionData['userId'],
			'Entrance' => 1,
			'kind' => 'W',
			'keyword' => $this->input->get('keyword')
		]);

		if ($response->rCode === null && !empty($response->Data)) {
			echo json_encode($response->Data);
		} else {
			echo json_encode([
				'Total' => 0,
				'AgentList' => []
			]);
		}
	}

	public function apiGetCombinationPlanInfo($planId)
	{
		global $userSessionData, $lionTravel;

		$filter["id"] = $planId;
		$filter["userid"] = $userSessionData["userId"];
		$filter['Entrance'] = 1;	//1:B2B 2:ERP
		$planData = getRequestV2("v2_PlanInfo", $filter);

		return $planData;
	}

}

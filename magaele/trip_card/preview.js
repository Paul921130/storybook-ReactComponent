import React from 'react';
import TripCard from './index.js';
import { storiesOf } from '@storybook/react';
let nowDate=0;
let Poidata={
      "category": "1",
      "id": "7516",
      "starttime": "1055",
      "endtime": "1155",
      "packageType": null,
      "name": "福岡機場發放餐盒(車上吃)",
      "thumbnail": "https://updmexapi.api.liontravel.com/photo/120.jpg",
      "isFavorite": false,
      "mode": "1",
      "duration": 60,
      "nowGuide": "7226",
      "startLongitude": 130.446752,
      "startLatitude": 33.590316,
      "endLongitude": 130.446752,
      "endLatitude": 33.590316,
      "guideList": [
        {
          "id": "7226",
          "name": "特色美食-日式壽司輕食+綠茶-午餐",
          "duration": 1,
          "brief": "品嘗日式壽司輕食+綠茶"
        }
      ],
      "poiList": null,
      "title": null,
      "addr": null,
      "astarttime": null,
      "aendtime": null,
      "dispatch": 0,
      "contact": null,
      "phone": null,
      "desc": null,
      "type": "poi",
      "off": null,
      "location": {
        "latitude": 33.590316,
        "longitude": 130.446752
      },
      "pid": "1_7516",
      "guideId": "7226",
      "startTime": "1055",
      "endTime": "1155",
      "nowGuideInfo": {
        "id": "7226",
        "name": "特色美食-日式壽司輕食+綠茶-午餐",
        "duration": 1,
        "brief": "品嘗日式壽司輕食+綠茶"
      },
      "latitude": 33.590316,
      "longitude": 130.446752
    };
let AirplaneData={
    "depart": [
      {
        "airlineName": "CI中華航空",
        "airplaneName": "CI110",
        "airlineThumbnail": "https://updmexapi.api.liontravel.com/photo/airlines/CI.png",
        "from": {
          "portId": "TPE",
          "port": "桃園國際機場",
          "portcity": "台北市",
          "latitude": 25.079593,
          "longitude": 121.234078,
          "date": "2017-08-03",
          "time": "06:50:00"
        },
        "to": {
          "portId": "FUK",
          "port": "福岡機場",
          "portcity": "福岡市",
          "latitude": 33.590316,
          "longitude": 130.446752,
          "date": "2017-08-03",
          "time": "09:55:00"
        },
        "duration": 125,
        "timezone": "0",
        "distance": {
          "routId": 0,
          "value": 67.1,
          "time": 72
        }
      }
    ],
    "back": [
      {
        "airlineName": "CI中華航空",
        "airplaneName": "CI117",
        "airlineThumbnail": "https://updmexapi.api.liontravel.com/photo/airlines/CI.png",
        "from": {
          "portId": "FUK",
          "port": "福岡機場",
          "portcity": "福岡市",
          "latitude": 33.590316,
          "longitude": 130.446752,
          "date": "2017-08-07",
          "time": "21:00:00"
        },
        "to": {
          "portId": "TPE",
          "port": "桃園國際機場",
          "portcity": "台北市",
          "latitude": 25.079593,
          "longitude": 121.234078,
          "date": "2017-08-07",
          "time": "22:10:00"
        },
        "duration": 130,
        "timezone": "0",
        "distance": {
          "routId": 0,
          "value": 28.3,
          "time": 37
        }
      }
    ],
    "id": "10928",
    "breturn": [
      {
        "airlineName": "CI中華航空",
        "airplaneName": "CI117",
        "airlineThumbnail": "https://updmexapi.api.liontravel.com/photo/airlines/CI.png",
        "from": {
          "portId": "FUK",
          "port": "福岡機場",
          "portcity": "福岡市",
          "latitude": 33.590316,
          "longitude": 130.446752,
          "date": "2017-08-07",
          "time": "21:00:00"
        },
        "to": {
          "portId": "TPE",
          "port": "桃園國際機場",
          "portcity": "台北市",
          "latitude": 25.079593,
          "longitude": 121.234078,
          "date": "2017-08-07",
          "time": "22:10:00"
        },
        "duration": 130,
        "timezone": "0",
        "distance": {
          "routId": 0,
          "value": 28.3,
          "time": 37
        }
      }
    ]
    };
/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('行程助手(模組)', module)
    .add('trip_card', () => (
        <div>
            <TripCard dataType="poi" data={Poidata}/>
            <TripCard dataType="airplane" type="from" data={nowDate==0 ? AirplaneData.depart:AirplaneData.back}/>
            <TripCard dataType="airplane" type="to" data={nowDate==0 ? AirplaneData.depart:AirplaneData.back}/>
        </div>
    ));

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';
import TripCard from '../../trip_card/components/Module';
import TripTitle from '../../trip_title/components/Module';
class Module extends Component {
    constructor (props) {
        super(props);
        this.state = {
            statename: 'state',
            error: null,
            isLoaded: false,
            data: null,
            nowDateData:null,
            nowDateItemList:null,
            nowDate:2
        };
        //陣列插入元素的function
        Array.prototype.insert = function (index, item) {
            this.splice(index, 0, item);
        };
        var nums = ["one", "two", "four"];
        nums.insert(2, 'three');
        console.log(nums);
    }
    /**
     * ## All Lifecycle: [see detail](https://reactjs.org/docs/react-component.html)
     * * React Lifecycle
     * > - componentDidMount()
     * > - shouldComponentUpdate(nextProps, nextState)
     * > - componentDidUpdate(prevProps, prevState)
     * > - componentWillUnmount()
     * * Will be removed in 17.0: [see detail](https://github.com/facebook/react/issues/12152)
     * > - componentWillMount()
     * > - componentWillReceiveProps(nextProps)
     * > - componentWillUpdate(nextProps, nextState)
   */
    componentDidMount () {
        console.log('componentDidMount');
        this.getData();//這裡我會撈資料
    }
    componentDidUpdate(){
    }
    // Your handle property functions
    handleClick (e) {
        console.log('handleClick');
    }
    // Your general property functions..
    func (param) {
        console.log('sample func');
    }
    
    getData(){
        this.setState({
          isLoaded: false,
        })
        fetch(
              '../../../src/itineraryObject.json', 
            { 
                mode: 'no-cors',
                method: 'GET',
                'Authorization': '123456',
            }
        
        )
          .then(res => res.json())
          .then(
            (data) => {
              this.setState({
                data: data,
                nowDateData:data.scheduleList[this.state.nowDate],
                nowDateItemList:data.scheduleList[this.state.nowDate].itemList,
                isLoaded: true,
                error:null,
              })
              console.log(data);
              console.log(data.scheduleList[this.state.nowDate]);
              console.log(data.scheduleList[this.state.nowDate].itemList);
            },
            (error) => {
              this.setState({
                isLoaded: true,
                error:error,
              });
              console.log(error)
            }
          )
        // .catch(e => console.log('錯誤:', e))
    }

    aJaxDone(){}
    
    addDate(date,days){ 
        var d=new Date(date); 
        d.setDate(d.getDate()+days); 
        var m=d.getMonth()+1; 
        return d.getFullYear()+'/'+m+'/'+d.getDate(); 
    }

    _createItineraryBlock(data){
        if(data==null){
            console.log(data)
            return(<span style={{color:'red'}}>哇！沒有撈到資料</span>)
        }else{
            return(
                <div className="itineraryBlock">
                    {this._createTitle(data)}
                    {this._createPoiCard(data)}
                </div>
            )
        }
    }

    _createTitle(data){
        console.log(data.scheduleList[this.state.nowDate].placeList[0].cityName);
        let departDate=data.departDate;
        let dateTitle=this.addDate(departDate, parseFloat(this.state.nowDate))
        let _data={
            title:dateTitle,
            day:this.state.nowDate,
            date: dateTitle,
            summary:[
                data.scheduleList[this.state.nowDate].placeList[0].cityName
            ]
        }
        return(
            <TripTitle titleType="tripSchedule" data={_data}/>
        )
    }
    mouseDownHandler(ev, data){
        console.log(data);
        // ev.nativeEvent.stopImmediatePropagation();
    }
    clickHandler(ev,data){
        console.log('clickHandler');
        if(ev.target.className=='near'){
            console.log("你點到了附近喲！")
        }
        if(ev.target.className=='self'){
            console.log("你點到了位置喲！")
        }
        if(ev.target.className=='detail'){
            console.log("你點到了詳情喲！")
        }
    }
    _createPoiCard(data){
        console.log(data.scheduleList[this.state.nowDate].itemList);
        //itemList就就是當日所含的景點陣列
        let itemList=data.scheduleList[this.state.nowDate].itemList;
        let poiCardArr=itemList.map((child,index)=>{
            console.log(child);
            console.log(index);
            return (<TripCard 
                        key={index} 
                        dataType="poi" 
                        data={child} 
                        onMouseDown={this.mouseDownHandler}
                        onClick={this.clickHandler}
                    />)
        })
        return poiCardArr;
    }
    /**
     * Render Notice：
     * 1. render 裡 setState 會造成回圈，setState -> render -> setState -> render ...
     * 2. 避免在 componentWillMount 調用 setState 或非同步行為，並且 componentWillMount 將被棄用，建議可放在 constructor 或 getDerivedStateFromProps。
     * 3. 不要使用 array 的 index 為 keys，可針對該內容 hash 後為 key
     */
    render () {
        const classes = classNames.bind(styles)('itinerary_block');
        return (
            <div className={classes} >
                {!this.state.isLoaded?<span>loading</span>:<span>{this._createItineraryBlock(this.state.data)}</span>}
                {this.props.children}
            </div>
        );
    }
}
/**
 * Props default value write here
 */
Module.defaultProps = {
    prop: 'string'
};
/**
 * Typechecking with proptypes, is a place to define prop api. [Typechecking With PropTypes](https://reactjs.org/docs/typechecking-with-proptypes.html)
 */
Module.propTypes = {
    prop: PropTypes.string.isRequired
};

export default Module;

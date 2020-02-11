import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

class Module extends Component {
    constructor (props) {
        super(props);
        this.state = {
            mapIsReady: false,
            showMap:true,
            statename: 'state'
        };
        if (!this.state.mapIsReady) {
            //先掛上googleMapAPI的DOM
            this._callGoogleMapAPI();
        }else{
            return;
        }
        console.log("outoff return")
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
    //引入googleMapAPI
    _callGoogleMapAPI(){
        const ApiKey = 'AIzaSyCGO5bWxnakmnsDVzWrhMhLqACbbwLf6JA';
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${ApiKey}`;
        script.async = false;
        script.defer = true;
        script.addEventListener('load', () => {
            this.setState({ mapIsReady: true });
        });
        this.once(document.body.appendChild(script));
    }
    componentDidMount() {
        console.log('componentDidMount');
    }
    componentDidUpdate(prevProps, prevState) {
        if (this.state.mapIsReady==true) {
            // Display the map
            let map = this.initMap();
            let markers =this.initMarkers(map);
            this.cityHoverHandler(markers);
            this._gMapFitBounds(map, markers);
            // markers[this.state.nowHover].setAnimation(google.maps.Animation.BOUNCE);
            // You also can add markers on the map below
        }
        console.log('componentDidUpdate');
    }
    //讓function只執行一次的function
    once(fn, context) { 
        let result;
        return function() { 
            if(fn) {
                result = fn.apply(context || this, arguments);
                fn = null;
            }
            return result;
        };
    }
    createMarker(map,lat,lng){
        let marker = new window.google.maps.Marker({
            position: {lat: lat, lng: lng}, 
            map: map,
        });
        return marker;
    }
    // Your handle property functions
    handleClick (e) {
        console.log('handleClick');
    }
    // Your general property functions..
    func (param) {
        console.log('sample func');
    }
    //城市列表hover事件（讓對應的marker會有跳動的動畫效果）
    cityHoverHandler(markers){
        for(let i =0;i<this.props.data.cityArr.length;i++){
            document.querySelector(".citylist"+i).addEventListener("mouseout", function(e){
                e.stopPropagation();
                e.preventDefault();             
                markers[i].setAnimation(null);
            });
            document.querySelector(".citylist"+i).addEventListener("mouseover", function(e){
                e.stopPropagation();
                e.preventDefault();
                markers[i].setAnimation(google.maps.Animation.BOUNCE);
            });
        }
    }
    //城市列表點擊事件（會獲得被點擊的城市的具體的DOM並且關閉地圖區塊）
    citySelectHandler(ev){
        console.log(ev.target);
        this.setState({
            showMap:false
        })
    }
    //建立地圖物件function
    initMap(){
        let map = new window.google.maps.Map(
            this.refs.map1,
            {
                center: {lat: 43.949317, lng: 4.805528},
                zoom: 8
            }
        );
        return map;
    }
    //生成Marker（組）的function
    initMarkers(map){
        let self=this;
        //建立location陣列，就是多個經緯度（用來渲染Marker的）
        let locations =this.props.data.cityArr;
        let markers=locations.map(function(location, i) {
            let marker= self.createMarker(map , location.lat , location.lng);
            return marker;
        });
        return markers;
    }
    _gMapFitBounds(map, markers){
        let bound = new google.maps.LatLngBounds();
        for(var i in markers){
            bound.extend(markers[i].getPosition());
        }
        if(this.props.fitbounds==true){
            map.fitBounds(bound);
        }else{
            return;
        }
    }
     //生成附近城市列表的li
    initCityList(){
        let self=this;
        let cityList=[];
        for(let i=0;i<this.props.data.cityArr.length;i++){
            cityList.push(
                <li 
                    id={"nearByCityLi"+`${i}`}
                    className={"citylist"+`${i}`}
                    index={i}
                    key={this.props.data.cityArr[i].id}
                    cityid={this.props.data.cityArr[i].id}
                    cityname={this.props.data.cityArr[i].name}
                    citylat={this.props.data.cityArr[i].lat}
                    citylng={this.props.data.cityArr[i].lng}
                    onClick={(ev)=>{this.citySelectHandler(ev)}}
                >
                <span className="num">{i+1}</span>
                {this.props.data.cityArr[i].name}
                </li>
            )
        }
        return cityList;
    }
    /**
     * Render Notice：
     * 1. render 裡 setState 會造成回圈，setState -> render -> setState -> render ...
     * 2. 避免在 componentWillMount 調用 setState 或非同步行為，並且 componentWillMount 將被棄用，建議可放在 constructor 或 getDerivedStateFromProps。
     * 3. 不要使用 array 的 index 為 keys，可針對該內容 hash 後為 key
     */
    render () {
        const classes = classNames.bind(styles)('map_nearby');
        return (
            <div className={classes} style={{display:this.state.showMap?null:'none'}}>
                <div className="cityBlock">
                    <div className='title'>
                        選擇 <span style={{color:'red'}}>{this.props.data.nowCity}</span> 相關城市
                    </div>
                    <div className='cityList'>
                        <ul>
                            {this.initCityList()}
                        </ul>
                    </div>
                </div>
                <div className='nearByCityMapBlock'>
                    <div className="google" ref="map1" />
                </div>
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

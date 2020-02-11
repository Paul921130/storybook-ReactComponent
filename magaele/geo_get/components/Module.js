import React, { Component } from 'react';
import PropTypes from 'prop-types';
import cx from 'classnames';
import styles from '../css.scss';

class Module extends Component {
    constructor (props) {
        super(props);
        this.state={
            Latitude:'',
            Longitude:'',
            mapIsReady:false,
            map:null
        }
        this.getGeo = this.getGeo.bind(this);
        this.positionInfo = this.positionInfo.bind(this);
        // this.getGeo();
        if (!this.state.mapIsReady) {
            //先掛上googleMapAPI的DOM
            this._callGoogleMapAPI();
        }else{
            return;
        }
    }
    getGeo=()=>{
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(this.positionInfo);
        } else { 
            console.log('瀏覽器不支援Geolocation');
        }
    }
    positionInfo=(position)=>{
        console.log(position);
        // alert('Latitude:'+position.coords.latitude+'\nLongitude:'+position.coords.longitude);
        this.setState({
            Latitude: position.coords.latitude,
            Longitude: position.coords.longitude
        })
        // x.innerHTML = "Latitude: " + position.coords.latitude + 
        // "<br>Longitude: " + position.coords.longitude;
    }
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
    componentDidUpdate(){
        if(this.state.mapIsReady && this.state.map==null){
            this.setState({
                map: this.initMap()
            })
        }
        this.initMap()
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
    //
    //建立地圖物件function
    initMap(){
        let {Latitude,Longitude}={...this.state}
        let map = new window.google.maps.Map(
            this.refs.map1,
            {
                center: {lat: Latitude!=='' ? Latitude : 43.949317, lng: Longitude!=='' ? Longitude : 4.805528},
                zoom: 20
            }
        );
        return map;
    }
    render () {
        const classes = cx.bind(styles)('geo_get');
        let {
           Latitude,
           Longitude
        }= {...this.state}
        return(
            <div className={classes}>
                <button onClick={()=>{this.getGeo()}}>Try It</button>
                <br/>
                <span>Latitude:{Latitude!==''? Latitude : 'Loading...'}</span>
                <br/>
                <span>Longitude:{Longitude!==''? Longitude : 'Loading...'}</span>
                <div className="map_container" ref="map1" />
            </div>
        )
    }
}

Module.defaultProps = {

};

Module.propTypes = {
    // prop: PropTypes.string.isRequired
};

export default Module;

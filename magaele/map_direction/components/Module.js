import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

class Module extends Component {
    constructor (props) {
        super(props);
        this.state = {
            mapIsReady: false,
            statename: 'state'
        };
        if (!this.state.mapIsReady) {
            //先掛上googleMapAPI的DOM
            this._callGoogleMapAPI();
        }else{
            return;
        }
        console.log("outoff return");
    }
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
        document.body.appendChild(script);
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
    }

    componentDidUpdate() {
        if (this.state.mapIsReady==true) {
          // Display the map
            let map = this.initMap();
            let directions=this.initDirections(map);
          //   let markers =this.initMarkers(map);
          // You also can add markers on the map below
        }
        console.log('componentDidUpdate');
    }

    initMap(){
        console.log("建立地圖的function");
        let map = new window.google.maps.Map(
            this.refs['map1'],
            {
                center: {lat: 43.949317, lng: 4.805528},
                zoom: 18
            }
        );
        return map;
    }

    initMarkers(){
        this.createMarker();
        console.log("建立marker群的地方");
    }
    initDirections(map){
        //設置起點
        let startLocation={lat: 43.949317, lng: 4.805528};
        //設置終點
        let endLocation={lat: 43.849317, lng: 4.905528};
        let directionsService = new google.maps.DirectionsService;
        let directionsDisplay = new google.maps.DirectionsRenderer;
        this._googleDirections(startLocation,endLocation,directionsService,directionsDisplay,map)
    }
    _googleDirections(origin, destination, service, directionsDisplay,map){
        service.route({
            origin: origin,
            destination: destination,
            travelMode:'DRIVING',
            provideRouteAlternatives: true,
            optimizeWaypoints: true
        },function(response, status) {
            if (status === google.maps.DirectionsStatus.OK) {
              console.log(response);
              directionsDisplay.setDirections(response);
              directionsDisplay.setMap(map);
            } else {
              if(status == "ZERO_RESULTS"){
                  //計算Google時間
                  service.route({
                    origin: origin,
                    destination: destination,
                    travelMode: 'TRANSIT',
                    provideRouteAlternatives: true,
                    optimizeWaypoints: true
                  }, function(response, status) {
                    if (status === google.maps.DirectionsStatus.OK) {
                      directionsDisplay.setDirections(response);
                      directionsDisplay.setMap(map);
                    } else {
                      console.log("error display directions due to:" + status, response);
                    }
                });
              }
              console.log("error display directions due to:" + status);
            }
          });
    }
    createMarker(){
        console.log("生成Marker的地方");
    }
    
    // Your handle property functions
    handleClick (e) {
        console.log('handleClick');
    }
    // Your general property functions..
    func (param) {
        console.log('sample func');
    }
    /**
     * Render Notice：
     * 1. render 裡 setState 會造成回圈，setState -> render -> setState -> render ...
     * 2. 避免在 componentWillMount 調用 setState 或非同步行為，並且 componentWillMount 將被棄用，建議可放在 constructor 或 getDerivedStateFromProps。
     * 3. 不要使用 array 的 index 為 keys，可針對該內容 hash 後為 key
     */
    render () {
        const classes = classNames.bind(styles)('map_direction');
        return (
            <div className={classes} >
                <h3>路線規劃模組</h3>
                <div className="google" ref="map1" />
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

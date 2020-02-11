import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';
import CityTab from '../../city_tab/components/Module';
class Module extends Component {
    constructor (props) {
        super(props);
        this.state = {
            statename: 'state',
            data:[
                {
                    cityName: '台北市', 
                    cityId:'00001'
                },
                {
                    cityName: '新北市', 
                    cityId:'00002'
                },
                {
                    cityName: '台中市', 
                    cityId:'00003'
                },
                {
                    cityName: '高雄市', 
                    cityId:'00004'
                },
                {
                    cityName: '宜蘭縣', 
                    cityId:'00005'
                },
                {
                    cityName: '花蓮縣', 
                    cityId:'00006'
                },
                {
                    cityName: '屏東縣', 
                    cityId:'00007'
                },
            ],
            nowSelectCity:0
        };
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
    // Your handle property functions
    handleClick (e) {
        console.log('handleClick');
    }
    cityTabClickHandler(ev){
        //阻止點擊事件傳遞
        ev.stopPropagation();
        //如果用ev.target會指到裡面<span>DOM,如果改成ev.currentTarget就會指到寫了onClick的DOM上
        console.log(ev.currentTarget.getAttribute("data-cityname"));
        console.log(ev.currentTarget.getAttribute("data-cityId"));
        console.log(ev.currentTarget.getAttribute("index"));
        let selectIndex=parseFloat(ev.currentTarget.getAttribute("index"));
        this.setState({
            nowSelectCity:selectIndex
        })   
    }
    addCityClickHandler(ev){
        console.log("點擊完成後會跳出選擇附近城市窗口")
    }
    removeCityClickHandler(ev){
        ev.stopPropagation();
        let removIndex=parseFloat(ev.currentTarget.parentElement.getAttribute("index"));
        let newData=[...this.state.data];
        /*
            splice會直接對原陣列進行更改
            千萬不要寫成 newArr=oldArr.splice(removIndex,1)
            這樣實際上newArr是被刪除的元素所組成的陣列
        */
        newData.splice(removIndex,1)
        this.setState({
            data:newData
        })
        //如果原本tab是處於selected的狀態的話，則將改選成第一個tab
        if(this.state.nowSelectCity==removIndex){
            this.setState({
                nowSelectCity:0
            })
        }
        console.log("點擊這裡就會刪除城市哦")
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
        const classes = classNames.bind(styles)('citytab_container');
        return (
            <div className={classes} >
                <CityTab
                    data={this.state.data}
                    nowSelectCity={this.state.nowSelectCity}
                    onClickCity={(ev)=>{this.cityTabClickHandler(ev)}}
                    addCityClick={(ev)=>{this.addCityClickHandler(ev)}}
                    removeCityClick={(ev)=>{this.removeCityClickHandler(ev)}}
                />
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

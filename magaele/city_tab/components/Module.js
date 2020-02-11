import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

let Module = (props) => {
    let datalength=props.data.length;
    //如果data太多就是會有這個tx的className,抓大於4的時候
    let tx =datalength>4?"t"+datalength:null;
    const classes = classNames.bind(styles)('city_tab');
    let createCityTab=(child, i)=>{
        if(i==props.nowSelectCity){
            return(
                <li
                    index={i}
                    className="active"
                    data-cityname={child.cityName}
                    data-cityid={child.cityId}
                    key={child.cityId}
                    onClick={(ev)=>{props.onClickCity(ev)}}>
                {child.cityName}
                    <span 
                        onClick={(ev)=>{props.removeCityClick(ev)}}>
                        x
                    </span>
                </li>
            )
        }else{
            return(
                <li
                    className={tx}
                    index={i}
                    data-cityname={child.cityName}
                    data-cityid={child.cityId}
                    key={child.cityId}
                    onClick={(ev)=>{props.onClickCity(ev)}}>
                    {child.cityName}
                    <span 
                        onClick={(ev)=>{props.removeCityClick(ev)}}>
                        x
                    </span>
                </li>
            )
        }
    }
    return (
        <div className={classes} >
            {props.data.map((child, i)=>createCityTab(child, i))}
            <li
                onClick={(ev)=>{props.addCityClick(ev)}}>
                <span>+</span>
            </li>
            {props.children}
        </div>
    );
};
/**
 * Props default value write here
 */
Module.defaultProps = {
    prop: 'string',
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
    ],
    nowSelectCity:3,
    onClickCity:()=>{
        console.log("缺少父層的onClick(onClickCity)喲")
    },
    addCityClick:()=>{
        console.log("缺少父層的onClick(onAddCityClick)喲")
    },
    removeCityClick:()=>{
        console.log("缺少父層的onClick(removeCityClick)喲")
    }
};
/**
 * Typechecking with proptypes, is a place to define prop api. [Typechecking With PropTypes](https://reactjs.org/docs/typechecking-with-proptypes.html)
 */
Module.propTypes = {
    prop: PropTypes.string.isRequired
};

export default Module;

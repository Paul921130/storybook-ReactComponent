import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

let Module = (props) => {
    let weekDay=['日','一','二','三','四','五','六'];
    let dayTitle=props.data.date+' ('+weekDay[new Date(props.data.date).getDay()]+')';
    let summaryArr=[];
    //如果summary的長度為1的時候就不要有“>”號了
    if(props.data.summary.length=1){
        summaryArr.push(props.data.summary[0])
    }else{
        for(let i=0;i<props.data.summary.length;i++){
            if(i==0){
                console.log("hihi");
                summaryArr.push(props.data.summary[i]+" >")
            }else if(i==props.data.summary.length-1){
                summaryArr.push(props.data.summary[i])
            }else{
                summaryArr.push(props.data.summary[i]+" 、")
            }
        }
    }
    const classes = classNames.bind(styles)('trip_title');
    if(props.titleType=="tripSchedule"){
        return (
            <div className={classes}>
                <div className='dayNum lion'>D{props.data.day+1}</div>
                <div className='dayTitle'>{dayTitle}</div>
                <div className='summary'>{summaryArr}</div>
                {props.children}
            </div>
        );
    }else if(props.titleType=="tripInfo"){
        return (
            <div
                style={{
                    borderBottom: '1px solid #EEE',
                }}  
                className={classes}>
                <div className='dayNum lion'>D{props.data.day+1}</div>
                <div className='cityTitle'>巴黎市</div>
            </div>
        );
    }else if(props.titleType=="tripInfoFlight"){
        return (
            <div
                style={{
                    borderBottom: '1px solid #EEE',
                }} 
                className={`${classes} flightTitle`}>
                <div className='dayNum lion'>
                    <i class="flight"></i>
                灰</div>
                <div className='cityTitle'>航班</div>
            </div>
        );
    }else{
        return(
            <h1 style={{color:'red'}}>props裡面的titleType有錯</h1>
        )
    }
    
};
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

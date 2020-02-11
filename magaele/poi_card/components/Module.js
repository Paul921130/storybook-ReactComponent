import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

let Module = (props) => {
    console.log(props.fakeProps);
    const classes = classNames.bind(styles)('poi_card');
    // let hihi=(ev,i)=>{
    //     console.log(JSON.stringify(ev.target.getAttribute("data")));
    // }
    let openMap=(ev,i)=>{
        console.log("你點到了觀看地圖");
    }
    let addPoi=(ev,i)=>{
        console.log("你點到了加入行程");
    }
    let getDetail=(ev,i)=>{
        console.log("你點到了查看詳情");
    }
    let mapOption=(options)=>{
        console.log(options.length);
        let optionArr=[];
        for(let i=0;i<options.length;i++){
            optionArr.push(<option
                key={i}
                data-duration={options[i].duration}
                value={i}
                >{options[i].title}</option>)
        }
        return optionArr;
    }
    return (
        <div className={classes} >
            <div className="singleItem" data-type={props.data.dataType} data-pid={props.data.dataId}>
                <div className="thumb">
                    <img 
                        src={props.data.imgSrc} 
                        onError={()=>{this.onerror=null;this.src="https://updmexapi.api.liontravel.com/photo/120.jpg"}}/> 
                </div>
                <div className={props.data.liked==true ? 'favoriteBtn active':'favoriteBtn'}></div>
                <div className="title">{props.data.poiTitle}</div>
                <div className="guideType">
                    <select name="guideSelect">
                        {mapOption(props.data.guideSelect)}
                    </select>
                </div>
                <div className="meta">
                    <span className="time">
                        <i className="lion clock-black"></i>
                        <span className="durationStr"> 0時30分</span>
                    </span>
                    <span className="timeape" onClick={()=>{openMap()}}>
                        <i className="lion location"></i>觀看地圖
                    </span>
                </div>
                <div className="toolBtn">
                    <div onClick={()=>{addPoi()}}>
                        <i className="lion plus-black"></i> 加入行程
                    </div>
                    <div onClick={()=>{getDetail()}}>
                        <i className="lion detail"></i> 查看詳情
                    </div>
                </div>
            </div>
            {props.children}
        </div>
    );
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

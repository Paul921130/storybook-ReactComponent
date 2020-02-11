import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

let Module = (props) => {
    console.log(props.data)
    let classes = classNames.bind(styles)('trip_card');
    let createCard=()=>{
        if(props.dataType=='poi'){
            classes=classes+' poi';
            return (
                <div 
                    className={classes} 
                    datatype='poi'
                    datapid='1_650'
                    datastarttime='15:50'
                    dataendtime='16:20'>
                    <div className='timeline'>
                        <span className='start'>{props.data.starttime}</span>
                        <span className='end'>{props.data.endtime}</span>
                    </div>
                    <div className='itemBlock' onMouseDown ={!props.onMouseDown ?null:(ev)=>{props.onMouseDown (ev, props.data)}}>
                        <div className='itemThumbnail'>
                            <img src={props.data.thumbnail}/>
                        </div>
                        <div className='itemInfo'>
                            <div className='title'>{props.data.name}</div>
                            <div className='detailBtn'>
                                <ul>
                                    <li className='near' 
                                        onClick={(ev)=>props.onClick (ev, props.data)}
                                    >
                                        <i className='lion location'></i> 附近
                                    </li>
                                    <li className='self'
                                        onClick={(ev)=>props.onClick (ev, props.data)}
                                    >
                                        <i className='lion map'></i> 位置
                                    </li>
                                    <li className="detail"
                                        onClick={(ev)=>props.onClick (ev, props.data)}
                                    >
                                        <i className='lion detail'></i> 詳情
                                    </li>
                                </ul>
                            </div>
                            <div className='time'>
                                <i className='lion clock'></i>
                                <input 
                                    className='durationPicker ui-timepicker-input'
                                    name='duration'
                                    value='00:30'
                                    autoComplete='off'
                                    readOnly
                                ></input>
                            </div>
                            <div className='guideType'>
                                <i className='lion bag'></i>
                                <select name='guideSelect'>
                                    <option
                                        value='675'
                                        dataduration='0.5 selected'
                                    >{props.data.nowGuideInfo.name}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            );
        }else if(props.dataType=='airplane'){
            classes=classes+' airplane';
            if(props.type=='from'){
                return (
                    <div className={classes} >
                        <div className='timeline'>
                            <span className='start'>{props.data[0].from.time.slice(0,-3)}</span>
                            <span className='end' style={{style:'none'}}></span>
                        </div>
                        <div className='itemBlock'>
                            <div className='itemThumbnail'>
                                <img src="http://localhost/pdm2/libraries/img/plane_takeoff_13263.png"/>
                            </div>
                            <div className='itemInfo'>
                                <div className='title'>{props.data[0].airplaneName}({props.data[0].from.port})</div>
                                <div className='time'>
                                    <i className='lion clock'></i>
                                    <span>起飛時間   {props.data[0].from.time.slice(0,-3)}</span>
                                </div>
                            </div>
                        </div>    
                    </div>
                );
            }else if(props.type=='to'){
                return (
                    <div className={classes} >
                        <div className='timeline'>
                            <span className='end' style={{style:'none'}}>{props.data[0].to.time.slice(0,-3)}</span>
                        </div>
                        <div className='itemBlock'>
                            <div className='itemThumbnail'>
                                <img src="http://localhost/pdm2/libraries/img/planelanding_avion_13264.png"/>
                            </div>
                            <div className='itemInfo'>
                                <div className='title'>{props.data[0].airplaneName}({props.data[0].to.port})</div>
                                <div className='time'>
                                    <i className='lion clock'></i>
                                    <span>降落時間   {props.data[0].to.time.slice(0,-3)}</span>
                                </div>
                            </div>
                        </div>    
                    </div>
                );
            }else{
                return(<h1 style={{color:'red'}}>你484沒給type</h1>)
            }
        }
    }
    return (
        createCard()
    );
};
/**
 * Props default value write here
 */
Module.defaultProps = {
    prop: 'string',
    onClick:()=>{console.log('你父層484沒有onClick的Props')},
    onMouseDown:()=>{console.log('你父層484沒有onMouseDown的Props')}
};
/**
 * Typechecking with proptypes, is a place to define prop api. [Typechecking With PropTypes](https://reactjs.org/docs/typechecking-with-proptypes.html)
 */
Module.propTypes = {
    prop: PropTypes.string.isRequired
};

export default Module;

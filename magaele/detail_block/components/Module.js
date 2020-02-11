import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

let Module = (props) => {
    let classes = classNames.bind(styles)('detail_block');
    let closeClick=()=>{
        console.log("您點到了關閉按鈕！")
    }
    let addFavoriteClick=()=>{
        console.log("您點到了收藏按鈕！")
    }
    classes = classes+' detailBlock'
    return (
        <div className={classes} >
            <div className='closeBtn' onClick={()=>{closeClick()}}></div>
            <div className='carousel slide' id='carousel-detail' dataride='carousel'>
                <div className='carousel-inner' role='listbox'>
                    <div className='item active'>
                        <a href="https://updmexapi.api.liontravel.com/photo/120.jpg" target="_blank" rel="prettyPhoto[1]">
                            <img src="https://updmexapi.api.liontravel.com/photo/120.jpg"/>
                        </a>
                    </div>
                    <a className="left carousel-control" href="#carousel-detail" role="button" data-slide="prev" style={{display: 'none'}}>
                        <img src="http://localhost/pdm2/libraries/img/pre_btn.png"/>
                    </a>
                    <a className="right carousel-control" href="#carousel-detail" role="button" data-slide="next" style={{display: 'none'}}>
                        <img src="http://localhost/pdm2/libraries/img/next_btn.png"/>
                    </a>
                </div>
                <div className="favoriteBtn" onClick={()=>{addFavoriteClick()}}></div>
                <div className="content singleItem" data-pid="1_25994" data-type="poi">
                    <h1>京都御所</h1>
                    <div className="guideType">
                        <select name="guideSelect">
                            <option value="28921" data-duration="120.0">入內參觀</option>
                        </select>
                    </div>
                    <div className="path"></div>
                    <div className="itemDetail">
                        <div>地址:
                            <span className="address">3 Kyōtogyoen， Kamigyō-ku， Kyōto-shi， Kyōto-fu 602-0881日本</span>
                        </div>
                        <div>營業時間: 
                            <span className="opentime">09:00-17:00</span>
                        </div>
                    </div>
                    <div className="guideInfo">
                        <div className="guideTitle" data-id="28921">入內參觀</div>
                        <div className="information" data-id="28921">京都御所除了能夠一窺當時天皇的生活空間及其演變外，御苑內豐富的植物花卉隨著季節呈現出不同的迷人姿態，建築上點綴有代表皇室的金色菊花，充滿低調莊嚴的美感。</div>
                    </div>
                    <div className="subPoiBlock" style={{display: 'none'}}></div>
                </div>
            </div>
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

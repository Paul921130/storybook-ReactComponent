import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

let Module = (props) => {
    const classes = classNames.bind(styles)('trip_list');
    return (
        <div className={classes} >
            <div className="title">
                <i className="lion bag-orange"></i>
                行程
            </div>
            <div className='info'>
                <p>1. 巴黎聖母</p>
                <p className='desc'>玩法: 入內參觀</p>
                <p>2. 羅浮宮</p>
                <p className='desc'>玩法: 參觀(含門票)</p>
                <p>3. 協和廣場</p>
                <p className='desc'>玩法: 下車參觀</p>
                <p>4. 杜樂麗花園</p>
                <p className='desc'>玩法: 下車參觀</p>
                <p>5. 瓦頓姆廣場</p>
                <p className='desc'>玩法: 下車參觀</p>
                <p>6. 新凱旋門</p>
                <p className='desc'>玩法: 參觀(含門票)</p>
                <p>7. 凱旋門</p>
                <p className='desc'>玩法: 參觀(含門票)</p>
                <p>8. 香榭麗舍大道</p>
                <p className='desc'>玩法: 下車參觀</p>
                <p>9. 花神咖啡館</p>
                <p className='desc'>玩法: 特色美食-品嘗咖啡</p>
                <p>10. 聖心堂</p>
                <p className='desc'>玩法: 入內參觀</p>
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

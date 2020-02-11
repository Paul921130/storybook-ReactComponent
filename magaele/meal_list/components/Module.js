import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

let Module = (props) => {
    const classes = classNames.bind(styles)('meal_list');
    return (
        <div className={classes} >
            <div className="title">
                <i className='lion meal-orange'></i>
                餐食
            </div>
            <div className="info">
                <p>
                    <span className='markBold'>早餐 / </span>
                    飯店內
                </p>
                <p>
                    花神咖啡館
                </p>
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

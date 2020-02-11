import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

let Module = (props) => {
    const classes = classNames.bind(styles)('hotel_list');
    return (
        <div className={classes} >
            <div className="title">
                <i className="lion hotel-orange"></i>
                旅館
            </div>
            <div className="info">
                <p>倫敦市政廳公園廣場酒店</p>
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

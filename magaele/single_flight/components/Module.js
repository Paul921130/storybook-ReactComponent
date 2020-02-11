import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

let Module = (props) => {
    const classes = classNames.bind(styles)('single_flight');
    return (
        <div className={classes} >
            <div className="date">2018-06-16</div>
            <div className="info">
                <img 
                    src="https://updmexapi.api.liontravel.com/photo/airlines/BR.png"
                    style={{
                        float:'left',
                        marginTop:'18px',
                        marginRight:'4px'
                    }}
                />
                <div style={{
                        marginRight:'40px',
                        display:'inline-block',
                        float:'left'
                    }}>
                    <br/>
                    BR長榮航空(BR087)
                    <br/>
                </div>
                <div className="flightTime">
                    <div className="departTime">
                        21:35  <span>倫敦希斯落機場(LHR)</span>
                    </div>
                    <div className='duration'>
                        15小時40分鐘 <span className="blue">直飛</span>
                    </div>
                    <div className='destinateTime'>
                        21:15  <span>桃園國際機場(TPE)</span>
                    </div>
                </div>
                <div className="clearfix"></div>
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

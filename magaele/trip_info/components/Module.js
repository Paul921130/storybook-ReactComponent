import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

class Module extends Component {
    constructor (props) {
        super(props);
        this.state = {
            statename: 'state',
            dayListArr:[1,2,3,4,5],
        };
        this.createFlightInfo=this.createFlightInfo.bind(this);
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
    createFlightInfo(data){
        return(
            <div className="listItem">
                <div className="title">
                    <i className="flight"></i>
                    航班
                </div>
                <div className="singleFlight">
                    <div className="date">2018-06-10</div>
                    <div className="info">
                        <img/>
                        <div className="flightTime">
                            <div className="departTime">
                                "23:40  "<span>桃園國際機場(TPE)</span>
                            </div>
                            <div className="duration">
                                "14小時50分鐘   "<span className="blue">直飛</span>
                            </div>
                            <div className="destinateTime">
                                "07:30  "<span>戴高樂機場(CDG)</span>
                            </div>
                        </div>
                        <div className="clearfix"></div>
                    </div>
                </div>
            </div>
        )
    }
    createDayInfo(data,index){
        return(
            <div>
                <span>Day{data}</span>
                <br/>
                <span></span>
                <br/>
            </div>
        )
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
        const classes = classNames.bind(styles)('trip_info');
        return (
            <div className={classes} >
                <div>
                    {this.createFlightInfo()}
                    {this.state.dayListArr.map((child,index) => this.createDayInfo(child,index))}
                </div>
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

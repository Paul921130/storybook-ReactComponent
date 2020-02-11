import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

class Module extends Component {
    constructor (props) {
        super(props);
        this.state = {
            statename: 'state',
            nowTab:'brief',
        };
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
    handleClickType(e){
        console.log(e.target.getAttribute("data-type"));
        this.setState({
            nowTab:e.target.getAttribute("data-type"),
        })
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
        const classes = classNames.bind(styles)('tab_page');
        return (
            <div className={classes} >
                <div className='tabBlock' style={{zIndex:"5"}}>
                    <ul className='mainTag' style={{marginBottom:'-4px'}}>
                        <li 
                            data-type='brief' 
                            className={this.state.nowTab=='brief'?"active":null} 
                            onClick={(ev)=>this.handleClickType(ev)}
                        >行程路線</li>
                        <li 
                            data-type='list'
                            className={this.state.nowTab=='list'?"active":null}  
                            onClick={(ev)=>this.handleClickType(ev)}
                        >行程資訊</li>
                    </ul>
                </div>
                <div className='tabContent briefView' style={this.state.nowTab=='brief'?null:{display:"none"}}>
                    <h1>行程路線區</h1>
                </div>
                <div className='itineraryBlock' style={this.state.nowTab=='list'?null:{display:"none"}}>
                    <h1>行程資料區</h1>
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

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

class Module extends Component {
    constructor (props) {
        super(props);
        this.state = {
            statename: 'state',
            title:'這裡是有state的組件喲！',
            totalDate:5,
            nowDate:0,
            editPage:true
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
        console.log(this.props.fakeProps);
    }
    componentDidUpdate(){
        //日期從0開始
        console.log("目前選擇到的是第"+`${this.state.nowDate}`+"日")
    }
    // Your handle property functions
    handleClick (e) {
        console.log('handleClick');
    }
    // Your general property functions..
    func (param) {
        console.log('sample func');
    }

    mapDate(totalDate){
        let dateArr=[];
        for(let i=0; i<totalDate; i++){
            dateArr.push(<li 
                key={i} 
                date={i}
                className={i==this.state.nowDate?'active':null}
                onClick={(ev)=>{this.clickDate(ev)}}
                >D{i+1}</li>);
        }
        if(this.state.editPage==true){
            dateArr.push(<li 
                key="add"
                onClick={()=>{this.addDate()}}
                className="add"
                ><span>+</span></li>)
        }
        return dateArr;
    }

    clickDate=(ev,i)=>{
        let clickedDate=parseFloat(ev.target.getAttribute("date"));
        console.log(ev.target)
        this.setState({
            nowDate:clickedDate,
        })
    }

    addDate=()=>{
        console.log("您點到了添加一天");
        this.setState((prevState)=>({
            totalDate:prevState.totalDate+1
        }))
    }
    /**
     * Render Notice：
     * 1. render 裡 setState 會造成回圈，setState -> render -> setState -> render ...
     * 2. 避免在 componentWillMount 調用 setState 或非同步行為，並且 componentWillMount 將被棄用，建議可放在 constructor 或 getDerivedStateFromProps。
     * 3. 不要使用 array 的 index 為 keys，可針對該內容 hash 後為 key
     */
    render () {
        const classes = classNames.bind(styles)('date_bar');
        return (
            <div className={classes} >
                <div className='bar_content'>
                    <ul>
                        {this.mapDate(this.state.totalDate)}
                    </ul>
                </div>
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

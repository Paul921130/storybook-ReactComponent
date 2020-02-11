import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

class Module extends Component {
    constructor (props) {
        super(props);
        this.state = {
            inputOpened:true,
            statename: 'state',
            inputValue:"",
            searchTxtValue:'',
        };
        this._inputKeyDownEvent = this._inputKeyDownEvent.bind(this);
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
    componentDidUpdate(){
       
    }
    // Your handle property functions
    handleClick (e) {
        console.log('handleClick');
    }
    inputOnChangeHandler(ev){
        let inputValue=ev.target.value;
        this.setState({
            inputValue:inputValue
        })
    }
    inputOnFocusHandler(ev){
        console.log("現在是onFocus的狀態喲");
        ev.target.addEventListener('keydown', this._inputKeyDownEvent);
    }
    inputOnBlurHandler(ev){
        console.log("現在是onBlur的狀態喲");
        ev.target.removeEventListener('keydown', this._inputKeyDownEvent);
    }
    //一定要記得把事件監聽的函數在constructor裡面做bind(this),讓callback的時候可以呼叫到“同一支函數”,這樣才能針對同一支callback做addEventListener跟removeEventListener！
    _inputKeyDownEvent(event){
        const keyCode = event.keyCode;
        if(this.state.inputValue.length>0){
            console.log(keyCode)
            if(keyCode==13){
                console.log("你按到Enter了！真棒")
                this._setValue();
            }
            if(keyCode==8){
                console.log("你按到backSpace了！真棒");
                this.setState({
                    inputValue:"",
                })
            }
        }else{
            if(keyCode==13){
                //enter鍵的keycode是13;
                console.log("input裡面沒有值誒")
            }
        }   
    }
    //輸入完成後可以獲取到現在input裡面的值
    _setValue(){
        console.log(this.state.inputValue);
        // fetch(
        //     "http://localhost/pdm2/planner/getPoiList2?categoryId=1&cityId=C101500001&circleId=&subCategory=&max=20&offset=1&keyword="+this.state.inputValue+""
        //   )
        //     // .then(res => res.json())
        //     .then(res => res.text())          // convert to plain text
        //     .then(text => console.log(text))
        //     // .then(res=>console.log(res)) 
        //     .then(
        //       (data) => {
        //         console.log(data)
        //       },
        //       (error) => {
        //         console.log("call API報錯")
        //         console.log(error)
        //       }
        // )
    }
    closeBtnClickHandler(){
        this.setState({
            inputValue:''
        })
        console.log("這裡點擊之後input裡面的值回清空喲")
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
        const classes = classNames.bind(styles)('input_box');
        return (
            <div className={this.state.inputOpened?'input_box active':'input_box'} >
                {this.props.children}
                    <div className="searchTxt" style={{display:"none"}}>{this.state.inputValue}</div>
                    <input 
                        type="text"
                        name="search"
                        value={this.state.inputValue||""}
                        aria-invalid="false"
                        className="valid"
                        style={{display:'inline-bock'}}
                        onChange={(ev)=>{this.inputOnChangeHandler(ev)}}
                        onFocus={(ev)=>{this.inputOnFocusHandler(ev)}}
                        onBlur={(ev)=>{this.inputOnBlurHandler(ev)}}
                        />
                    <div 
                        onClick={()=>{this.closeBtnClickHandler()}}
                        className="closeBtn"
                        style={{display:this.state.inputValue==''?'none':'block'}}>
                        <h5>X</h5>
                    </div>
                    <div className="searchBtn"></div>
             
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

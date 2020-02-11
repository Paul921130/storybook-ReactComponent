import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

class Module extends Component {
    constructor (props) {
        super(props);
        this.state = {
            statename: 'state',
            scrollNowTop:0,
            bottomNavOpened:false,
        };
        // 或者在 constructor 中声明
        this.header = React.createRef();
        this.bottomNav = React.createRef();
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
        window.addEventListener("scroll",()=>{this.scrollHeaderHandler()})
        window.addEventListener("scroll",()=>{this.scrollbottomNavHandler()})
        this.preventBodyScroll();
    }
    componentDidUpdate(prevProps, prevState){
        this.preventBodyScroll();
    }
    preventBodyScroll(){
        if(!this.state.bottomNavOpened){
            console.log("正關著");
            document.body.style.overflow="auto";
        }else{
            console.log("正開著")
            document.body.style.overflow="hidden";
        }
    }
    scrollHeaderHandler(){
        if(!this.state.bottomNavOpened){
            let header=this.header.current;
            let scrollTop=document.body.scrollTop || document.documentElement.scrollTop;
            if(scrollTop<700){
                header.style.position="relative";
            }else if(scrollTop>700){
                header.style.position="fixed";
            }
        }else{
            return
        }
    }
    scrollbottomNavHandler(){
        //判斷目前bottomNav是開啟的還是關閉的
        if(!this.state.bottomNavOpened){
            let bottomNav=this.bottomNav.current;
            let scrolldown=this.scrollUpOrDown();
            let scrollTop=document.body.scrollTop || document.documentElement.scrollTop;
            if(scrolldown){
                bottomNav.style.height="50px";
                if(this.scrollBottomHandler()==true){
                    bottomNav.style.height="0px";
                }
            }else{
                bottomNav.style.height="0px";
            }
        }else{
            return
        }
    }
    scrollBottomHandler(){
        let scrollTop=document.body.scrollTop || document.documentElement.scrollTop;
        //文檔總高度
        var scrollHeight = 0,bSH = 0,dSH = 0;
    　　if(document.body){
    　　　　bSH = document.body.scrollHeight;
    　　}
    　　if(document.documentElement){
    　　　　dSH = document.documentElement.scrollHeight;
    　　}
        scrollHeight = (bSH - dSH > 0) ? bSH : dSH ;
        //文檔總高度end
        //瀏覽器窗口高度
        var windowHeight = 0;
    　　if(document.compatMode == "CSS1Compat"){
    　　　　windowHeight = document.documentElement.clientHeight;
    　　}else{
    　　　　windowHeight = document.body.clientHeight;
    　　}
        //瀏覽器窗口高度end
        //返回是不是滾動到底部的布林值
        return scrollTop + windowHeight == scrollHeight;
    }
    scrollUpOrDown(){
        let scrollTop=document.body.scrollTop || document.documentElement.scrollTop;
        if(this.state.scrollNowTop<=scrollTop){
            this.setState({
                scrollNowTop:scrollTop
            })
            // console.log("往下滚动");
            return true;
        }else{
            this.setState({
                scrollNowTop:scrollTop
            })
            // console.log("往上滚动");
            return false;
        }
    }
    // Your handle property functions
    handleClick (e) {
        console.log('handleClick');
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
        const classes = classNames.bind(styles)('scroll_box');
        let headerH=this.props.headerH;
        return (
            <div className={classes} >
                {this.props.children}
                <div className="header" ref={this.header} style={{height:this.props.headerH}}/>
                {this.bottomNav?<div className="bottomNav" ref={this.bottomNav} onClick={(ev)=>{this.setState({bottomNavOpened:!this.state.bottomNavOpened})}} style={{height:this.state.bottomNavOpened ? 'calc(100vh - ' +headerH+ ')':"50px"}}/>:null}
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

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import styles from '../css.scss';

class Module extends Component {
    constructor (props) {
        super(props);
        this.state = {
            statename: 'state',
            searchValue:"",
            categoryId:"1",
            cityId:"C202000048",
            subCategory:"",
            circleId:"",
            max:20
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
    goSearchClick(){
        console.log('準備搜尋');
        console.log(this.state.searchValue);
        this.searchFun();
    }
    inputValueChange(e){
        console.log(e.currentTarget.value);
        this.setState({
            searchValue:e.currentTarget.value,
        });
    }
    // Your general property functions..
    func (param) {
        console.log('sample func');
    }
    searchFun(){
        let categoryId = this.state.categoryId;
	    let cityId = this.state.cityId;
    	let subCategory = this.state.subCategory;
	    let circleId = this.state.circleId;
        let max = this.state.max;
        let offset=1;
        //http://localhost/PDM2.0.1/planner/getPoiList2?categoryId=1&cityId=C202000048&circleId=&subCategory=&max=20&offset=1&keyword=%E5%AF%BA
        console.log("最多可搜出"+max+"筆資料");
        fetch(
            'http://localhost/PDM2.0.1/planner/getPoiList2?'+
            'categoryId='+categoryId+'&'+
            'cityId='+cityId+'&'+
            'circleId='+circleId+'&'+
            'subCategory='+subCategory+'&'+
            'max='+max+'&'+
            'offset='+offset+'&'+
            'keyword='+this.state.searchValue
          )
            .then(res => res.json())
            .then(
              (data) => {
                // this.setState({
                //   users: data.data,
                //   isLoaded: true,
                //   error:null,
                // })
                console.log(data)
              },
              (error) => {
                // this.setState({
                //   isLoaded: true,
                //   error:error,
                // });
                console.log(error)
              }
            )
    }
    /**
     * Render Notice：
     * 1. render 裡 setState 會造成回圈，setState -> render -> setState -> render ...
     * 2. 避免在 componentWillMount 調用 setState 或非同步行為，並且 componentWillMount 將被棄用，建議可放在 constructor 或 getDerivedStateFromProps。
     * 3. 不要使用 array 的 index 為 keys，可針對該內容 hash 後為 key
     */
    render () {
        const classes = classNames.bind(styles)('search_input');
        return (
            <div className={classes} >
                {this.props.test}
                <input 
                    type="text" 
                    name="search" 
                    value={this.state.searchValue} 
                    aria-invalid="false" 
                    className="valid"
                    onChange={(e)=>this.inputValueChange(e)}
                >
                </input>
                <span style={{
                    border:"3px solid blue",
                    marginLeft:'4px',
                    color:"#ffffff",
                    backgroundColor:"blue",
                    borderRadius:"5px",
                    cursor:"pointer"
                }} onClick={()=>{this.goSearchClick()}}>
                    Search!
                </span>
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

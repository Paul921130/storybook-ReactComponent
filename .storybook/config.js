import 'babel-polyfill';
import { configure } from '@storybook/react';
// import '../magaele/core/core.scss';

// 這一段styleshhet是demo icon模組用的
const showstyle = `.icon_lists li{
    float:left;
    width: 150px;
    height:180px;
    text-align: center;
  }
  .icon_lists{
    list-style-type: none;
  }
  .icon_lists .ic_rcln{
    font-size: 42px;
    line-height: 100px;
    margin: 10px 0;
    color:#333;
    -webkit-transition: font-size 0.25s ease-out 0s;
    -moz-transition: font-size 0.25s ease-out 0s;
    transition: font-size 0.25s ease-out 0s;
  }
  .icon_lists .ic_rcln:hover{
    font-size: 100px;
  }`;
  
  let sheet = document.createElement('style')
  sheet.innerHTML = showstyle;
  document.body.appendChild(sheet);

const req = require.context('../magaele', true, /preview\.js$/);

function loadStories() {
    req.keys().map(req);
}

configure(loadStories, module);
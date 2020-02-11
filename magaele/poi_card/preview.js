import React from 'react';
import PoiCard from './index.js';
import { storiesOf } from '@storybook/react';
let data={
    dataType:'spot',
    dataId:'1_987',
    imgSrc:'https://updmexapi.api.liontravel.com/photo/120.jpg',
    liked:true,
    poiTitle:'太宰府天滿宮',
    guideSelect:[
        {
            title:"入內參觀1",
            duration:30.0
        },
        {
            title:"入內參觀2",
            duration:45.0
        },
        {
            title:"入內參觀3",
            duration:60.0
        },
        {
            title:"入內參觀4",
            duration:45.0
        }
    ]
}
/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('行程助手(模組)', module)
    .add('poi_card', () => (
        <div>
            <h3>poi_card元件</h3>
            <PoiCard fakeProps="我來傳個假props" data={data}/>
            <PoiCard fakeProps="我來傳個假props" data={data}/>
            <PoiCard fakeProps="我來傳個假props" data={data}/>
            <PoiCard fakeProps="我來傳個假props" data={data}/>
        </div>
    ));

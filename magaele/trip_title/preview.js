import React from 'react';
import TripTitle from './index.js';
import { storiesOf } from '@storybook/react';

let data={
    title:'2018/06/07',
    day:1,
    date:'2018/06/07',
    summary:[
        "台北市",
        "田尻町",
        "京都市"
    ]
}
/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('行程助手(模組)', module)
    .add('trip_title', () => (
        <div>
            <h3>行程路線的Title</h3>
            <div>
                <TripTitle titleType="tripSchedule" data={data}/>    
            </div>
            <h3>行程資訊的單日title</h3>
            <div>
                <TripTitle titleType="tripInfo" data={data}/>
            </div>    
            <h3>行程資訊的航班title</h3>
            <div>
                <TripTitle titleType="tripInfoFlight" data={data}/>
            </div> 
        </div>
    ));

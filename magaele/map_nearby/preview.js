import React from 'react';
import MapNearby from './index.js';
import { storiesOf } from '@storybook/react';

const data={
    nowCity:'巴黎市',
    cityArr:[
        {
            id:'C101500135',
            name:'亞維儂',
            lat:43.949317,
            lng:4.805528
        },
        {
            id:'C101500134',
            name:'沃克呂茲省',
            lat:44.056505,
            lng:5.143207
        },
        {
            id:'C101500132',
            name:'戈爾代',
            lat:43.911315,
            lng:5.200176
        },
        {
            id:'C101500136',
            name:'加爾省',
            lat:43.9447,
            lng:4.151376
        },
        {
            id:'C101500131',
            name:'亞爾',
            lat:43.676647,
            lng:4.627777
        },
        {
            id:'C101500029',
            name:'馬賽',
            lat:43.296482,
            lng:5.36978
        },
        {
            id:'C101500023',
            name:'坎城',
            lat:43.552847,
            lng:7.017369
        },
        {
            id:'C101500019',
            name:'格拉斯',
            lat:43.660153,
            lng:6.926492
        },
        {
            id:'C101500021',
            name:'尼斯',
            lat:43.710173,
            lng:7.261953
        },
        {
            id:'C102300102',
            name:'杜林',
            lat:45.070312,
            lng:7.686857
        },
        {
            id:'C101500057',
            name:'霞慕尼',
            lat:45.923697,
            lng:6.869433
        },
        {
            id:'C102300051',
            name:'米蘭',
            lat:45.465422,
            lng:9.185924
        }
    ],
};

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('行程助手(模組)', module)
    .add('map_nearby', () => (
        <div>
            <MapNearby
                data={data}
                fitbounds={true}
            />
        </div>
    ));

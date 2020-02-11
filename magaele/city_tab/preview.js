import React from 'react';
import CityTab from './index.js';
import { storiesOf } from '@storybook/react';
let data={
    data:[
        {
            cityName: '台北市', 
            cityId:'00001'
        },
        {
            cityName: '新北市', 
            cityId:'00002'
        },
        {
            cityName: '台中市', 
            cityId:'00003'
        },
        {
            cityName: '高雄市', 
            cityId:'00004'
        },
    ],
    nowSelectCity:2
}

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('行程助手(模組)', module)
    .add('city_tab', () => (
        <div>
            <CityTab 
                data={data.data}
                
            />
        </div>
    ));

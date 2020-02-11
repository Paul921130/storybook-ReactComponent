import React from 'react';
import GeoGet from './components/Module';
import { storiesOf } from '@storybook/react';

storiesOf('隨便做做', module)
    .add('geo_get', () => (
        <div>
            <h3>獲取使用者位置</h3>
            <GeoGet/>
        </div>
    ));

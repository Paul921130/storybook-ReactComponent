import React from 'react';
import TrafficInfo from './index.js';
import { storiesOf } from '@storybook/react';

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('undefined', module)
    .add('traffic_info', () => (
        <div>
            <TrafficInfo/>
        </div>
    ));

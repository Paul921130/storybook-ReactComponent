import React from 'react';
import TripInfo from './index.js';
import { storiesOf } from '@storybook/react';

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('undefined', module)
    .add('trip_info', () => (
        <div>
            <TripInfo />
        </div>
    ));

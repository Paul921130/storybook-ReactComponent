import React from 'react';
import TripList from './index.js';
import { storiesOf } from '@storybook/react';

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('undefined', module)
    .add('trip_list', () => (
        <div>
            <TripList />
        </div>
    ));

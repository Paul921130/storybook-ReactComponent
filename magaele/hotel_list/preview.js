import React from 'react';
import HotelList from './index.js';
import { storiesOf } from '@storybook/react';

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('undefined', module)
    .add('hotel_list', () => (
        <div>
            <HotelList />
        </div>
    ));

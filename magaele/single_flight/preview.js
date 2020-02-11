import React from 'react';
import SingleFlight from './index.js';
import { storiesOf } from '@storybook/react';

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('行程助手(模組)', module)
    .add('single_flight', () => (
        <div>
            <SingleFlight/>
        </div>
    ));

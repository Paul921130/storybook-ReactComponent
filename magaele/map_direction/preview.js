import React from 'react';
import MapDirection from './index.js';
import { storiesOf } from '@storybook/react';

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('行程助手(模組)', module)
    .add('map_direction', () => (
        <div>
            <MapDirection/>
        </div>
    ));

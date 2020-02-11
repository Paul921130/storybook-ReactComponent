import React from 'react';
import MealList from './index.js';
import { storiesOf } from '@storybook/react';

/**
 * ## [Storybook Tutorial](https://www.learnstorybook.com/)
 */
storiesOf('undefined', module)
    .add('meal_list', () => (
        <div>
            <MealList />
        </div>
    ));

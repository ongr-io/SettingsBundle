import React from 'react';
import SettingsTable from './components/SettingsTable'

var App = React.createClass({
    render: function () {
        return (
            <div>
            <h1>General Settings</h1>
            <SettingsTable />
            </div>
        );
    }
});

export default App;


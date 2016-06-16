import React, { Component } from 'react'
import {BootstrapTable, TableHeaderColumn} from 'react-bootstrap-table'; 
import { Button } from 'react-bootstrap';
import $ from 'jquery';


var actionButtons = (<Button bsStyle="primary" onClick={onClickEdit}>Edit</Button>)

function onClickEdit(){
  console.log("click")
}

const setting_types = ['bool', 'string', 'array', 'object']

function boolFormatter(value, row){
  return 'boolHandling ' + value; 
}

function objectFormatter(value, row){
  return 'objectHandling ' + value;   
}

function stringFormatter(value, row){
  return 'stringHandling ' + value;   
}

function arrayFormatter(value, row){
  return 'arrayHandling ' + value;    
}

function valueFormatter(value, row){
  return value.toString();
  switch(row.setting_type){
      case 'bool': return boolFormatter(value, row);
      case 'object': return objectFormatter(value, row);
      case 'string': return stringFormatter(value, row);
      case 'array': return arrayFormatter(value, row);
      default: return value;  
  }
}   


class SettingsTable extends Component {
  constructor(props){
    super(props);
    this.state = {};
  };

  componentDidMount(){
    $.getJSON("../data.json", function(result) {
      this.setState({data: this.filterInput(result)});
    }.bind(this))
  };

  filterInput(data){
    return data.documents
  };

  render(){
      if(this.state.data){
        return (
            <BootstrapTable data={this.state.data} striped={true} hover={true} search={true}>
                <TableHeaderColumn isKey={true} hidden={true} dataField="id">ID</TableHeaderColumn>
                <TableHeaderColumn dataField="name" dataSort={true}>Name</TableHeaderColumn>
                <TableHeaderColumn dataField="description" dataSort={true}>Description</TableHeaderColumn>
                <TableHeaderColumn dataField="type" dataSort={true}>Type</TableHeaderColumn>
                <TableHeaderColumn dataField="value" dataSort={false} dataFormat={valueFormatter}>Value</TableHeaderColumn>
            </BootstrapTable>
        )
        
      }else{
        return (<p>Loading</p>)
      }
  }
}

export default SettingsTable

        
// <TableHeaderColumn dataField="setting_actions">Actions</TableHeaderColumn>


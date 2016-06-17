import React, { Component } from 'react'
import {BootstrapTable, TableHeaderColumn} from 'react-bootstrap-table'; 
import { Button } from 'react-bootstrap';
import $ from 'jquery';



//const setting_types = ['bool', 'string', 'array', 'object']

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

function onClickEdit(id){
  console.log("edit", id)
}

function onClickDelete(id){
  console.log("delete", id)
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
    var dataWithButtons = data.documents.map(function(el){
      el.actions = (
        <div>
          <Button bsStyle="primary" onClick={() => onClickEdit(el.id)}>Edit</Button>
          <Button bsStyle="primary" onClick={() => onClickDelete(el.id)}>Delete</Button>
        </div>
      )
      return el
    });

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
                <TableHeaderColumn dataField="actions">Actions</TableHeaderColumn>
            </BootstrapTable>
        )
        
      }else{

        return (<p>Loading</p>)

      }
  }
}

export default SettingsTable

        
// <TableHeaderColumn dataField="setting_actions">Actions</TableHeaderColumn>


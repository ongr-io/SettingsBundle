import React, { Component } from 'react'
import {BootstrapTable, TableHeaderColumn} from 'react-bootstrap-table'; 

const settings = [
  {
      setting_name: "Setting 1",
      setting_desc: "Desc 1",
      setting_type: "bool",
      setting_value: "true"
  },{
      setting_name: "Setting 2",
      setting_desc: "Desc 2",
      setting_type: "object",
      setting_value: "object"
  },{
      setting_name: "Setting 3",
      setting_desc: "Desc 3",
      setting_type: "string",
      setting_value: "string"
  },{
      setting_name: "Setting 4",
      setting_desc: "Desc 4",
      setting_type: "array",
      setting_value: "[1, 3, 5]"
  }
];

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
  switch(row.setting_type){
      case 'bool': return boolFormatter(value, row);
      case 'object': return objectFormatter(value, row);
      case 'string': return stringFormatter(value, row);
      case 'array': return arrayFormatter(value, row);
      default: return value;  
  }
}   

function onRowSelect(row, isSelected){
  console.log(row);
  console.log("selected: " + isSelected)
}

    
const selectRowProp = {
  mode: "radio",
  clickToSelect: true,
  bgColor: "rgb(167, 167, 167)",
  onSelect: onRowSelect
}

const edit_type = {
    type: "select",
    options:{
        values: setting_types
    }
}

const edit_value = {
    type: "textarea"
}

function onAfterSaveCell(row, cellName, cellValue) {
  console.log("Save cell '"+cellName+"' with value '"+cellValue+"'");
  console.log("Thw whole row :");
  console.log(row);

  console.log(row.setting_type)
  switch(row.setting_type){
      case 'bool': row.setting_value='true'; break;
      case 'object': row.setting_value='newobject'; break;
      case 'string': row.setting_value='newstring'; break;
      case 'array': row.setting_value='newarray'; break;
      default: row.setting_value='';         
  }
}

const cellEditProp = {
  mode: "click",
  blurToSave: true,
  afterSaveCell: onAfterSaveCell
}

class SettingsTable extends Component {

  render(){
      return (
          <BootstrapTable data={settings} striped={true} hover={true} search={true} insertRow={true} deleteRow={true} selectRow={selectRowProp} cellEdit={cellEditProp}>
              <TableHeaderColumn isKey={true} dataField="setting_name" dataSort={true}>Name</TableHeaderColumn>
              <TableHeaderColumn dataField="setting_desc" dataSort={true}>Description</TableHeaderColumn>
              <TableHeaderColumn dataField="setting_type" dataSort={true} editable={edit_type}>Type</TableHeaderColumn>
              <TableHeaderColumn dataField="setting_value" dataSort={true} dataFormat={valueFormatter} editable={edit_value}>Value</TableHeaderColumn>
          </BootstrapTable>
      )
  }
}

export default SettingsTable





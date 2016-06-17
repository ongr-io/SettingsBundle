import React from 'react'
import {BootstrapTable, TableHeaderColumn} from 'react-bootstrap-table'; 
import { Button, ButtonGroup, Modal } from 'react-bootstrap';
import $ from 'jquery';

//const setting_types = ['bool', 'string', 'array', 'object']

var SettingsTable = React.createClass({

  getInitialState () {
    return {
      showModal: false
    };
  },

  componentDidMount(){
    $.getJSON("../data.json", function(result) {
      this.setState({data: this.filterInput(result)});
    }.bind(this))
  },

  filterInput(data){
    console.log(this.state)
    var dataWithButtons = data.documents.map(function(el){
      el.actions = (
        <ButtonGroup>
          <Button bsStyle="primary"  onClick={() => this.openModal(el.id)}>Edit</Button>
          <Button bsStyle="primary" onClick={() => this.onClickDelete(el.id)}>Delete</Button>
        </ButtonGroup>
      )
      return el
    }, this);

    return data.documents
  },

  closeModal() {
    console.log('close')
    this.setState({ showModal: false });
  },

  openModal(id) {
    console.log('open', id)
    this.setState({ showModal: true });
  },

  onClickDelete(id){
    console.log("delete", id)
  },

  boolFormatter(value, row){
    return 'boolHandling ' + value; 
  },

  objectFormatter(value, row){
    return 'objectHandling ' + value;   
  },

  stringFormatter(value, row){
    return 'stringHandling ' + value;   
  },

  arrayFormatter(value, row){
    return 'arrayHandling ' + value;    
  },

  valueFormatter(value, row){
    return value.toString();
    // todo add switch for different types
    switch(row.type){
        case 'bool': return boolFormatter(value, row);
        case 'object': return objectFormatter(value, row);
        case 'string': return stringFormatter(value, row);
        case 'array': return arrayFormatter(value, row);
        default: return value;  
    }
  },

  render(){
      if(this.state.data){
        
        return (
            <div>
            <BootstrapTable data={this.state.data} striped={true} hover={true} search={true}>
                <TableHeaderColumn isKey={true} hidden={true} dataField="id">ID</TableHeaderColumn>
                <TableHeaderColumn dataField="name" dataSort={true}>Name</TableHeaderColumn>
                <TableHeaderColumn dataField="description" dataSort={true}>Description</TableHeaderColumn>
                <TableHeaderColumn dataField="type" dataSort={true}>Type</TableHeaderColumn>
                <TableHeaderColumn dataField="value" dataSort={false} dataFormat={this.valueFormatter}>Value</TableHeaderColumn>
                <TableHeaderColumn dataField="actions">Actions</TableHeaderColumn>
            </BootstrapTable>
            <Modal show={this.state.showModal} onHide={this.closeModal}>
              <Modal.Header closeButton>
                <Modal.Title>Modal heading</Modal.Title>
              </Modal.Header>
              <Modal.Body>
                <h4>Text in a modal</h4>
                <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>

                <hr />

                <h4>Overflowing text to show scroll behavior</h4>
                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
               </Modal.Body>
              <Modal.Footer>
                <Button onClick={this.closeModal}>Close</Button>
              </Modal.Footer>
            </Modal>
            </div>
        )
        
      }else{

        return (
          <p>Loading</p>
        )

      }
  }

});

export default SettingsTable




  
  

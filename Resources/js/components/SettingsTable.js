import React from 'react'
import {BootstrapTable, TableHeaderColumn} from 'react-bootstrap-table'; 
import { Button, ButtonGroup, Modal, Form, FormGroup, Col, ControlLabel, FormControl } from 'react-bootstrap';
import $ from 'jquery';

//const setting_types = ['bool', 'string', 'array', 'object']

var SettingsTable = React.createClass({

  getInitialState () {
    return {
      showModal: false,
      editSetting: {}
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
          <Button bsStyle="primary"  onClick={() => this.openModal(el)}>Edit</Button>
          <Button bsStyle="primary" onClick={() => this.onClickDelete(el.id)}>Delete</Button>
        </ButtonGroup>
      )
      return el
    }, this);

    return data.documents
  },

  closeModal() {
    this.setState({ 
      showModal: false ,
      editSetting: {}
    });
  },

  openModal(id) {
    this.setState({ 
      showModal: true,
      editSetting: id 
    });
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
                <Modal.Title>Edit setting</Modal.Title>
              </Modal.Header>
              <Modal.Body>

                <Form horizontal>
                  <FormGroup controlId="formName">
                    <Col componentClass={ControlLabel} sm={2}>
                      Name
                    </Col>
                    <Col sm={10}>
                      <FormControl type="text" placeholder="Edit name" defaultValue={this.state.editSetting.name} />
                    </Col>
                  </FormGroup>

                  <FormGroup controlId="formDesc">
                    <Col componentClass={ControlLabel} sm={2}>
                      Description
                    </Col>
                    <Col sm={10}>
                      <FormControl type="text" placeholder="Edit descr" defaultValue={this.state.editSetting.description} />
                    </Col>
                  </FormGroup>

                </Form>

              </Modal.Body>
              <Modal.Footer>
                <Button bsStyle="primary">Save</Button>
                <Button bsStyle="danger" onClick={this.closeModal}>Close</Button>
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




  
  

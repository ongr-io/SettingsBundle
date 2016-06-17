import React from 'react'
import {BootstrapTable, TableHeaderColumn} from 'react-bootstrap-table'; 
import { Button, ButtonGroup, Modal, Form, FormGroup, Col, ControlLabel, FormControl } from 'react-bootstrap';
import $ from 'jquery';
import findIndex from 'lodash.findIndex'
import clone from 'lodash.clone'
import remove from 'lodash.remove'

//const setting_types = ['bool', 'string', 'array', 'object']

var SettingsTable = React.createClass({

  getInitialState () {
    return {
      showModalEdit: false,
      showModalAdd: false,
      editSetting: {},
      addSetting: {}
    };
  },

  componentDidMount(){
    $.getJSON("../data.json", function(result) {
      this.setState({data: this.filterInput(result)});
    }.bind(this))
  },
  
  addActionButtons(el){
    return (
        <ButtonGroup>
          <Button bsStyle="primary"  onClick={() => this.openModalEdit(el)}>Edit</Button>
          <Button bsStyle="primary" onClick={() => this.onClickDelete(el)}>Delete</Button>
        </ButtonGroup> 
      )
  },

  filterInput(data){
    var dataWithButtons = data.documents.map(function(el){
      el.actions = this.addActionButtons(el)
      return el
    }, this);

    return data.documents
  },

  openModalEdit(id) {
    this.setState({ 
      showModalEdit: true,
      editSetting: id 
    });
  },

  closeModalEdit() {
    this.setState({ 
      showModalEdit: false,
      editSetting: {}
    });
  },

  openModalAdd() {
    this.setState({ 
      showModalAdd: true,
      addSetting: {
        description: "",
        id: "",
        name: "",
        profile: "default",
        type: "",
        value: ""  
      }
    });
  },
  
  closeModalAdd() {
    this.setState({ 
      showModalAdd: false,
      addSetting: {}
    });
  },

  sendToAPI(path, data){
    console.log("API Call", path, data)
    // todo remove return to activate API Calls
    return

    $.ajax({
      type: 'POST',
      url: path,
      data: data
    })
    .done(function(data) {
      console.log('success')
    })
    .fail(function(jqXhr) {
      console.log('error', jqXhr);
    });
  },

  saveModalEdit(){
    // update edited element in React state
    var el = this.state.editSetting
    var index = findIndex(this.state.data, function(o) { return o.id == el.id; });
    this.state.data[index] = el

    // send edited element to API 
    var o = clone(el);
    delete o["actions"]; 
    this.sendToAPI('./setting/edit/'+el.id, JSON.stringify(o))

    this.closeModalEdit();
  },

  saveModalAdd(){
    // get element from form
    var el = this.state.addSetting
    
    // build id, todo check for unique id
    el.id = el.profile+'_'+el.name
    
    // send added element to API 
    this.sendToAPI('./setting/add', JSON.stringify(el))

    // add element in React state
    el.actions = this.addActionButtons(el);
    this.state.data.push(el)

    this.closeModalAdd();
  },

  handleChangeEditDescr(event){
    this.state.editSetting.description = event.target.value
  },
  
  handleChangeAddName(event){
    this.state.addSetting.name = event.target.value
  },

  handleChangeAddDescr(event){
    this.state.addSetting.description = event.target.value
  },

  onClickDelete(el){
    
    // send API call to delete setting
    delete el["actions"]; 
    this.sendToAPI('./setting/delete/'+el.id, JSON.stringify(el))

    // delete element from state
    remove(this.state.data, function(o) { return o.id==el.id})
    this.setState({ 
      data: this.state.data
    });

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

  renderTable(data){
    return (
      <BootstrapTable data={data} striped={true} hover={true} search={true}>
        <TableHeaderColumn isKey={true} hidden={true} dataField="id">ID</TableHeaderColumn>
        <TableHeaderColumn dataField="name" dataSort={true}>Name</TableHeaderColumn>
        <TableHeaderColumn dataField="description" dataSort={true}>Description</TableHeaderColumn>
        <TableHeaderColumn dataField="type" dataSort={true}>Type</TableHeaderColumn>
        <TableHeaderColumn dataField="value" dataSort={false} dataFormat={this.valueFormatter}>Value</TableHeaderColumn>
        <TableHeaderColumn dataField="actions">Actions</TableHeaderColumn>
      </BootstrapTable>
    )
  },

  renderModalEdit(state){
    return (
      <Modal show={state.showModalEdit} onHide={this.closeModalEdit}>
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
                <FormControl type="text" placeholder="Edit name" defaultValue={state.editSetting.name} disabled={true}/>
              </Col>
            </FormGroup>

            <FormGroup controlId="formProfile">
              <Col componentClass={ControlLabel} sm={2}>
                Profile
              </Col>
              <Col sm={10}>
                <FormControl type="text" placeholder="Edit profile" defaultValue={state.editSetting.profile} disabled={true}/>
              </Col>
            </FormGroup>

            <FormGroup controlId="formDesc">
              <Col componentClass={ControlLabel} sm={2}>
                Description
              </Col>
              <Col sm={10}>
                <FormControl type="text" placeholder="Edit descr" defaultValue={state.editSetting.description} onChange={this.handleChangeEditDescr}/>
              </Col>
            </FormGroup>

          </Form>

        </Modal.Body>
        <Modal.Footer>
          <Button bsStyle="primary" onClick={this.saveModalEdit}>Save</Button>
          <Button bsStyle="danger" onClick={this.closeModalEdit}>Close</Button>
        </Modal.Footer>
      </Modal>
    )
  },

  renderModalAdd(state){
    return (
      <Modal show={state.showModalAdd} onHide={this.closeModalAdd}>
        <Modal.Header closeButton>
          <Modal.Title>Add setting</Modal.Title>
        </Modal.Header>
        <Modal.Body>

          <Form horizontal>
            <FormGroup controlId="formName">
              <Col componentClass={ControlLabel} sm={2}>
                Name
              </Col>
              <Col sm={10}>
                <FormControl type="text" placeholder="Add name" onChange={this.handleChangeAddName}/>
              </Col>
            </FormGroup>

            <FormGroup controlId="formProfile">
              <Col componentClass={ControlLabel} sm={2}>
                Profile
              </Col>
              <Col sm={10}>
                <FormControl type="text" placeholder="Add profile" defaultValue='default' disabled={true}/>
              </Col>
            </FormGroup>

            <FormGroup controlId="formDesc">
              <Col componentClass={ControlLabel} sm={2}>
                Description
              </Col>
              <Col sm={10}>
                <FormControl type="text" placeholder="Edit descr" defaultValue={state.editSetting.description} onChange={this.handleChangeAddDescr}/>
              </Col>
            </FormGroup>

          </Form>

        </Modal.Body>
        <Modal.Footer>
          <Button bsStyle="primary" onClick={this.saveModalAdd}>Save</Button>
          <Button bsStyle="danger" onClick={this.closeModalAdd}>Close</Button>
        </Modal.Footer>
      </Modal>
    )
  },

  render(){
      if(this.state.data){

        return (
            <div>
              <Button bsStyle="primary" onClick={this.openModalAdd}>Add setting</Button>
              {this.renderTable(this.state.data)}
              {this.renderModalEdit(this.state)}
              {this.renderModalAdd(this.state)}
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




  
  

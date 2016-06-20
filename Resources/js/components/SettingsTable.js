import React from 'react'
import {BootstrapTable, TableHeaderColumn} from 'react-bootstrap-table';
import {Button, ButtonGroup, Modal, Form, FormGroup, Col, ControlLabel, FormControl, Radio} from 'react-bootstrap';
import $ from 'jquery';
import findIndex from 'lodash.findIndex'
import clone from 'lodash.clone'
import remove from 'lodash.remove'

var SettingsTable = React.createClass({

    // set initial state of the SettingsTable Class
    getInitialState () {
        return {
            showModalEdit: false,
            showModalAdd: false,
            editSetting: {},
            addSetting: {}
        };
    },

    // load setting data from json and set it to the state
    componentDidMount(){
        // todo: change source from file to url
        $.getJSON("./settings/search", function (result) {
            this.setState({data: this.handleInput(result)});
        }.bind(this))
    },

    // add action buttons to data, for display in column
    handleInput(data){
        var dataWithButtons = data.documents.map(function (el) {
            el.actions = this.addActionButtons(el)
            return el
        }, this);

        return data.documents
    },

    // action buttons edit and delete
    addActionButtons(el){
        return (
            <ButtonGroup>
                <Button bsStyle="primary" onClick={() => this.openModalEdit(el)}>Edit</Button>
                <Button bsStyle="primary" onClick={() => this.onClickDelete(el)}>Delete</Button>
            </ButtonGroup>
        )
    },

    // functions to open, close and save the modals
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
                type: "bool",
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

    saveModalEdit(){
        // update edited element in React state
        var el = this.state.editSetting
        var index = findIndex(this.state.data, function (o) {
            return o.id == el.id;
        });
        this.state.data[index] = el

        // send edited element to API
        var o = clone(el);
        delete o["actions"];
        this.sendToAPI('./setting/edit/' + el.id, JSON.stringify(o))

        this.closeModalEdit();
    },
    saveModalAdd(){
        // get element from form
        var el = this.state.addSetting

        // build id, todo check for unique id
        el.id = el.profile + '_' + el.name

        // send added element to API
        this.sendToAPI('./setting/add', JSON.stringify(el))

        // add element in React state
        el.actions = this.addActionButtons(el);
        this.state.data.push(el)

        this.closeModalAdd();
    },


    // change handler for the form fields
    handleChangeEditDescr(event){
        this.state.editSetting.description = event.target.value
    },
    handleChangeEditType(event){
        this.state.editSetting.type = event.target.value
        this.state.editSetting.value = ""
        this.forceUpdate()
    },
    handleChangeEditValue(event){
        this.state.editSetting.value = event.target.value
        console.log(this.state.editSetting)
    },
    handleChangeAddName(event){
        this.state.addSetting.name = event.target.value
    },
    handleChangeAddProfile(event){
        this.state.addSetting.profile = event.target.value
    },
    handleChangeAddDescr(event){
        this.state.addSetting.description = event.target.value
    },
    handleChangeAddType(event){
        this.state.addSetting.type = event.target.value
        this.state.addSetting.value = ""
        this.forceUpdate()
    },
    handleChangeAddValue(event){
        this.state.addSetting.value = event.target.value
    },

    // click handler to delete a setting
    onClickDelete(el){
        // send API call to delete setting
        delete el["actions"];
        this.sendToAPI('./setting/delete/' + el.id, JSON.stringify(el))

        // delete element from state
        remove(this.state.data, function (o) {
            return o.id == el.id
        })
        this.setState({
            data: this.state.data
        });
    },

    // API calls
    sendToAPI(path, data){
        console.log("API Call", path, data);

        $.ajax({
            type: 'POST',
            url: path,
            data: data
        })
            .done(function (data) {
                console.log('success')
            })
            .fail(function (jqXhr) {
                console.log('error', jqXhr);
            });
    },

    valueFormatter(value, row){
        // return value.toString();
        return 'kk';
    },

    renderTable(data){
        return (
            <BootstrapTable
                data={data}
                striped={true}
                remote={ true }
                hover={true}
                search={true}
                pagination={true}>
                <TableHeaderColumn isKey={true} hidden={true} dataField="id">ID</TableHeaderColumn>
                <TableHeaderColumn dataField="name" dataSort={true}>Name</TableHeaderColumn>
                <TableHeaderColumn dataField="description" dataSort={true}>Description</TableHeaderColumn>
                <TableHeaderColumn dataField="type" dataSort={true}>Type</TableHeaderColumn>
                <TableHeaderColumn dataField="value" dataSort={false}
                                   dataFormat={this.valueFormatter}>Value</TableHeaderColumn>
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
                                <FormControl type="text" placeholder="Edit name" defaultValue={state.editSetting.name}
                                             disabled={true}/>
                            </Col>
                        </FormGroup>

                        <FormGroup controlId="formProfile">
                            <Col componentClass={ControlLabel} sm={2}>
                                Profile
                            </Col>
                            <Col sm={10}>
                                <FormControl type="text" placeholder="Edit profile"
                                             defaultValue={state.editSetting.profile} disabled={true}/>
                            </Col>
                        </FormGroup>

                        <FormGroup controlId="formDesc">
                            <Col componentClass={ControlLabel} sm={2}>
                                Description
                            </Col>
                            <Col sm={10}>
                                <FormControl type="text" placeholder="Edit descr"
                                             defaultValue={state.editSetting.description}
                                             onChange={this.handleChangeEditDescr}/>
                            </Col>
                        </FormGroup>

                        <FormGroup controlId="formType">
                            <Col componentClass={ControlLabel} sm={2}>
                                Type
                            </Col>
                            <Col sm={10}>
                                <FormControl componentClass="select" defaultValue={state.editSetting.type}
                                             onChange={this.handleChangeEditType}>
                                    <option value="bool">bool</option>
                                    <option value="string">string</option>
                                    <option value="array">array</option>
                                    <option value="object">object</option>
                                </FormControl>
                            </Col>
                        </FormGroup>

                        { this.renderValueSwitchEdit(state.editSetting.value) }

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
                                <FormControl componentClass="select" placeholder="select"
                                             onChange={this.handleChangeAddProfile}>
                                    <option value="default">default</option>
                                    <option value="custom">custom</option>
                                </FormControl>
                            </Col>
                        </FormGroup>

                        <FormGroup controlId="formDesc">
                            <Col componentClass={ControlLabel} sm={2}>
                                Description
                            </Col>
                            <Col sm={10}>
                                <FormControl type="text" placeholder="Edit descr"
                                             defaultValue={state.editSetting.description}
                                             onChange={this.handleChangeAddDescr}/>
                            </Col>
                        </FormGroup>

                        <FormGroup controlId="formType">
                            <Col componentClass={ControlLabel} sm={2}>
                                Type
                            </Col>
                            <Col sm={10}>
                                <FormControl componentClass="select" placeholder="select"
                                             onChange={this.handleChangeAddType}>
                                    <option value="bool">bool</option>
                                    <option value="string">string</option>
                                    <option value="array">array</option>
                                    <option value="object">object</option>
                                </FormControl>
                            </Col>
                        </FormGroup>

                        { this.renderValueSwitchAdd() }

                    </Form>

                </Modal.Body>
                <Modal.Footer>
                    <Button bsStyle="primary" onClick={this.saveModalAdd}>Save</Button>
                    <Button bsStyle="danger" onClick={this.closeModalAdd}>Close</Button>
                </Modal.Footer>
            </Modal>
        )
    },

    renderValueSwitchAdd(){
        switch (this.state.addSetting.type) {
            case 'bool':
                return this.renderValueBoolAdd();
                break;
            case 'string':
                return this.renderValueStringAdd();
                break;
            default:
                return (<p>value change</p>);
                break;
        }
    },

    renderValueBoolAdd(){
        return (
            <FormGroup controlId="formValueBool">
                <Col componentClass={ControlLabel} sm={2}>
                    Setting
                </Col>
                <Col sm={10}>
                    <FormControl componentClass="select" onChange={this.handleChangeAddValue}>
                        <option></option>
                        <option value={true}>true</option>
                        <option value={false}>false</option>
                    </FormControl>
                </Col>
            </FormGroup>
        )
    },

    renderValueStringAdd(){
        return (
            <FormGroup controlId="formValueString">
                <Col componentClass={ControlLabel} sm={2}>
                    Setting
                </Col>
                <Col sm={10}>
                    <FormControl type="text" defaultValue='string' onChange={this.handleChangeAddValue}/>
                </Col>
            </FormGroup>
        )
    },

    renderValueSwitchEdit(value){
        switch (this.state.editSetting.type) {
            case 'bool':
                return this.renderValueBoolEdit(value);
                break;
            case 'string':
                return this.renderValueStringEdit(value);
                break;
            default:
                return (<p>value change</p>);
                break;
        }
    },

    renderValueBoolEdit(val){
        return (
            <FormGroup controlId="formValueBool">
                <Col componentClass={ControlLabel} sm={2}>
                    Setting
                </Col>
                <Col sm={10}>
                    <FormControl componentClass="select" defaultValue={val} onChange={this.handleChangeEditValue}>
                        <option></option>
                        <option value={true}>true</option>
                        <option value={false}>false</option>
                    </FormControl>
                </Col>
            </FormGroup>
        )
    },

    renderValueStringEdit(value){
        return (
            <FormGroup controlId="formValueString">
                <Col componentClass={ControlLabel} sm={2}>
                    Setting
                </Col>
                <Col sm={10}>
                    <FormControl type="text" defaultValue={value} onChange={this.handleChangeEditValue}/>
                </Col>
            </FormGroup>
        )
    },

    // main render method: display table if data is loaded
    render(){
        if (this.state.data) {
            return (
                <div>
                    <Button bsStyle="primary" onClick={this.openModalAdd}>Add setting</Button>
                    {this.renderTable(this.state.data)}
                    {this.renderModalEdit(this.state)}
                    {this.renderModalAdd(this.state)}
                </div>
            )
        } else {
            return (
                <p>Loading</p>
            )
        }
    }

});

export default SettingsTable




  
  

const modalAddTeacher = document.getElementById("modalAddTeacher");
const btnAddTeacher = document.getElementById("btnAddTeacher");
const closeModalAddTeacher = document.getElementsByClassName("close-modal-add-teacher");

const personalDataBox = document.getElementById("link-tabs-box-1");
const addressDataBox = document.getElementById("link-tabs-box-2");
const qualificationsDataBox = document.getElementById("link-tabs-box-3");

handleTabBoxAction = () => {
    personalDataBox.onclick = function() {
        handlerTabBoxActive(this);
    }
    addressDataBox.onclick = function() {
        handlerTabBoxActive(this);
    }
    qualificationsDataBox.onclick = function() {
        handlerTabBoxActive(this);
    }
}
handleTabBoxAction();

handlerTabBoxActive = (anchor) => {
    const tabLinkList = document.querySelectorAll("#modalAddTeacher .nav-tabs li");
    tabLinkList.forEach(element => {
        element.classList.remove("active");
    });
    anchor.parentElement.classList.add("active");

    
    const tabBoxList = document.querySelectorAll("#modalAddTeacher .modal-body .tab-pane");
    tabBoxList.forEach(element => {
        element.classList.add("hidden-element");
    });
    const tabName = (anchor.getAttribute("id")).toString().replace("link-","");
    const tabBox = document.getElementById(tabName);
    tabBox.classList.remove("hidden-element")
}

handlerModalAction = () => {
    btnAddTeacher.onclick = function() {
        handleShowModal(modalAddTeacher);
        handleResetModal();
        document.getElementById("control_action_delete").classList.add("hidden-element");
    }

    for(const element in closeModalAddTeacher) {
        if(closeModalAddTeacher[element].classList){
            closeModalAddTeacher[element].onclick = function() {
                handleHideModal(modalAddTeacher);
            }
        }
    }

    // window.onclick = function(event) {
    //     if (event.target == modalAddTeacher) {
    //         handleHideModal(modalAddTeacher);
    //     }
    // }
}
handlerModalAction();

handleResetModal = () => {
    handlerTabBoxActive(personalDataBox);
    document.getElementById("form_data_teacher").reset();
    const qualifications = document.querySelectorAll("#form_data_teacher #tabs-box-3 fieldset .new-qualification-container");
    qualifications.forEach( (element, index) => {
        if(index != 0){
            element.outerHTML = "";
        }
    });

    const inputsRequiredFalse = document.querySelectorAll("#form_data_teacher [isValid='false']");
    if(inputsRequiredFalse.length > 0) {
        for(const element in inputsRequiredFalse){
            try {
                if(!inputsRequiredFalse[element].value){
                    inputsRequiredFalse[element].setAttribute("isValid", false);
                };
                const inputName = inputsRequiredFalse[element].getAttribute("name");
                inputsRequiredFalse[element].style.border = "1px solid black";
                const displayErrors = (inputName == "address_city_origin" || inputName == "address_city") ?
                    inputsRequiredFalse[element].parentElement.children[3] : inputsRequiredFalse[element].parentElement.children[2];
                displayErrors.innerText = "";
            } catch (error) {}
        }
    }

    const inputsRequiredTrue = document.querySelectorAll("#form_data_teacher [isValid='true']");
    if(inputsRequiredTrue.length > 0) {
        for(const element in inputsRequiredTrue){
            try {
                if(!inputsRequiredTrue[element].value){
                    inputsRequiredTrue[element].setAttribute("isValid", false);
                };
                const inputName = inputsRequiredFalse[element].getAttribute("name");
                inputsRequiredTrue[element].style.border = "1px solid black";
                const displayErrors = (inputName == "address_city_origin" || inputName == "address_city") ?
                    inputsRequiredFalse[element].parentElement.children[3] : inputsRequiredFalse[element].parentElement.children[2];
                displayErrors.innerText = "";
            } catch (error) {}
        }
    }

    document.getElementById("form_action").value = "add";
    document.getElementById("form_ref").value = "";
};

handleHideModal = (modal) => {
    modal.style.display = "none";
}

handleShowModal = async (modal) => {
    modal.style.display = "block";
    await handleGetFormDataList();
}

handleFilterTeacherList = (input) => {
}

handleValidateCEP = (input) => {
}

var qualificationLevelListOption = "";
handlerAddNewQualification = (anchor) => {
    anchor.parentElement.outerHTML = "";
    const index = document.querySelectorAll("#form_data_teacher #tabs-box-3 fieldset .new-qualification-container").length;
    const html = `
        <div class="new-qualification-container">
            <hr>
            <input type="hidden" name="qualification_ref[${index}]" id="qualification_form_ref${index}" value="">
            <div class="form-group col-md">
                <label for="qualification_name${index}" class="required">Curso</label>
                <input type="text" name="qualification_name[${index}]" id="qualification_name${index}" oninput="handleValidateFormInput(this)" isValid="false">
            </div>

            <div class="form-group col-md">
                <label for="qualification_level${index}" class="required">N??vel</label>
                <select name="qualification_level[${index}]" id="qualification_level${index}" onchange="handleValidateFormInput(this)" isValid="false">
                    <option selected="selected" value="">Selecione uma op????o...</option>
                    ${qualificationLevelListOption}
                </select>
            </div>

            <div class="form-group col-md">
                <label for="qualification_institution${index}" class="required">Insitui????o</label>
                <input type="text" name="qualification_institution[${index}]" id="qualification_institution${index}" oninput="handleValidateFormInput(this)" isValid="false">
            </div>

            <div class="form-group col-md">
                <label for="qualification_document${index}" class="required">Anexar certificado <strong>(PDF)</strong></label>
                <div id="document_path${index}">
                    <input type="file" name="qualification_document[${index}]" id="qualification_document${index}">
                </div>
            </div>

            <div class="form-group col-md">
                <label for="qualification_country${index}" class="required">Pa??s</label>
                <input type="text" name="qualification_country[${index}]" id="qualification_country${index}" value="Brasil" oninput="handleValidateFormInput(this)" isValid="true">
            </div>

            <div class="form-group col-md">
                <label for="qualification_state${index}" class="required">Estado</label>
                <input type="text" name="qualification_state[${index}]" id="qualification_state${index}" oninput="handleValidateFormInput(this)" isValid="false">
            </div>

            <div class="form-group col-sm">
                <label for="qualification_started${index}" class="required">In??cio</label>
                <input type="date" name="qualification_started[${index}]" id="qualification_started${index}" onchange="handleValidateFormInput(this)" isValid="false">
            </div>

            <div class="form-group col-sm">
                <label for="qualification_end${index}" class="required">Conclus??o</label>
                <input type="date" name="qualification_end[${index}]" id="qualification_end${index}" onchange="handleValidateFormInput(this)" isValid="false">
            </div>

            <div class="form-group col-md">
                <p class="required">Estado atual do curso</p>
                <div class="gap-md qualification-concluded-container">
                    <div>
                        <input type="radio" class="qualification-concluded" name="qualification_concluded[${index}]" id="is_concluded${index}" value="1">
                        <label style="display: inline;" for="is_concluded${index}">Conclu??do</label>
                    </div>
                    <div>
                        <input type="radio" class="qualification-concluded" name="qualification_concluded[${index}]" id="not_concluded${index}" value="0">
                        <label style="display: inline;" for="not_concluded${index}">Cursando</label>
                    </div>
                </div>
            </div>

        </div>

        <div class="form-group col-lg">
            <a href="#" id="add_new_qualification" onclick="handlerAddNewQualification(this)">
                <i class="fa-solid fa-plus"></i> Inclu??r nova forma????o
            </a>
        </div>
    `;
    document.querySelector("#form_data_teacher #tabs-box-3 fieldset").insertAdjacentHTML("beforeend", html);
}


handlerSetRowData = (data) => {
    const teacherData = data.reduce( (prev, curr) => {
        const tr = document.createElement("tr");
        tr.classList.add("row-body");
        tr.setAttribute("ondblclick", "handleGetToEdit(this)");
        tr.setAttribute("ref", curr.id);
        for(const element in curr) {
            if(element != "id"){
                if(document.querySelector(`#grid-container .item-head[headerName="${element}"]`)){
                    const td = document.createElement("td");
                    td.classList.add("item-body");
                    td.setAttribute("onclick", "handleFocusRow(this)");
                    td.innerText = curr[element];
                    tr.append(td);
                }
            }
        }

        prev += tr.outerHTML;
        return prev;
    }, "");

    document.querySelector("#grid-container .grid-body").innerHTML = teacherData;
    document.getElementById("grid-container").classList.remove("hidden-element");
}


handlerUpdateRowData = (data, action) => {
    const trFocus = document.querySelector(".grid-body .row-body.row-focus");
    switch (action) {
        case "add":
            const tr = document.createElement("tr");
            tr.classList.add("row-body");
            tr.setAttribute("ondblclick", "handleGetToEdit(this)");
            tr.setAttribute("ref", data.id);
            for(const element in data) {
                if(element != "id"){
                    if(document.querySelector(`#grid-container .item-head[headerName="${element}"]`)){
                        const td = document.createElement("td");
                        td.classList.add("item-body");
                        td.setAttribute("onclick", "handleFocusRow(this)");
                        td.innerText = data[element];
                        tr.append(td);
                    }
                }
            }

            if(document.getElementById("grid-container").classList.contains("hidden-element")){
                document.getElementById("grid-container").classList.remove("hidden-element");
                document.getElementById("without-data").classList.add("hidden-element");
            }

            document.querySelector("#grid-container .grid-body").append(tr);
            break;

        case "edit":
            trFocus.innerHTML = "";
            for(const element in data) {
                if(element != "id"){
                    if(document.querySelector(`#grid-container .item-head[headerName="${element}"]`)){
                        const td = document.createElement("td");
                        td.classList.add("item-body");
                        td.setAttribute("onclick", "handleFocusRow(this)");
                        td.innerText = data[element];
                        trFocus.append(td);
                    }
                }
            }

            if(document.getElementById("grid-container").classList.contains("hidden-element")){
                document.getElementById("grid-container").classList.remove("hidden-element");
                document.getElementById("without-data").classList.add("hidden-element");
            }

            document.querySelector("#grid-container .grid-body").append(trFocus);
            break;

        case "delete":
            trFocus.outerHTML = "";

            if(!document.querySelector(".grid-body .row-body")){
                document.getElementById("grid-container").classList.add("hidden-element");
                document.getElementById("without-data").classList.remove("hidden-element");
            }

            break;
    
        default:
            break;
    }
}

handlerPutValuesFields = (data) => {
    for(const element in data) {
        const input = document.querySelector(`#form_data_teacher .form-container [name="${element}"]`);
        if(input) {
            if(input.getAttribute("type") == "radio") {
                const radio = document.querySelector(`#form_data_teacher .form-container [name="${element}"][value="${data[element]}"]`);
                if(radio) radio.checked = true;
            } else {
                input.value = data[element];
            }
        }
    }


    const qtyQualifications = data.qualification.length;
    const {qualification} = data;
    if(qtyQualifications > 0) {
        qualification.forEach( (value, index) => {
            for(const element in value){
                if(element == "qualification_document"){
                    if(value[element]){
                        document.getElementById(`document_path${index}`).innerHTML = `
                            <a href="${value[element]}" target="_blank" rel="noopener noreferrer" class="document_path_view">Ver certificado</a>
                            <i class="fa-solid fa-trash" style="color:var(--red-500);cursor:pointer;" onclick="handlerResetDocumentFile(this, ${index}, '${value[element]}');"></i>
                        `;
                    }
                } else {
                    const input = document.querySelector(`#form_data_teacher .form-container [name="${element}[${index}]"]`);
                    if(input) {
                        if(input.getAttribute("type") == "radio") {
                            const radio = document.querySelector(`#form_data_teacher .form-container [name="${element}[${index}]"][value="${value[element]}"]`);
                            if(radio) radio.checked = true;
                        } else {
                            input.value = value[element];
                        }
                    }
                }
            }

            if(index < (qtyQualifications - 1)){
                handlerAddNewQualification(document.getElementById("add_new_qualification"));
            }
        });
    }

    document.getElementById("control_action_delete").classList.remove("hidden-element");
}

handlerResetDocumentFile = (file, index, path) => {
    file.classList.remove("fa-solid", "fa-trash");
    file.classList.add("fa", "fa-sync", "fa-spin");
    file.style.pointerEvents = "none";
    const ref = document.querySelector(`#form_data_teacher .form-container [name="qualification_ref[${index}]"]`).value;
    handleDeleteFileQualification(ref, file, index, path);
}

handleFocusRow = (cell) => {
    const rowFocus = document.querySelector("#grid-container .grid-body .row-body.row-focus");
    if(rowFocus) rowFocus.classList.remove("row-focus");
    
    const cellFocus = document.querySelector("#grid-container .grid-body .row-body .item-body.cell-focus");
    if(cellFocus) cellFocus.classList.remove("cell-focus");

    cell.parentElement.classList.add("row-focus");
    cell.classList.add("cell-focus");
}

handleValidateFormInput = (input) => {

    try {
        input.setAttribute("isValid", true);
    
        const inputName = (input.getAttribute("name")).split("[")[0];
        const inputValue = input.value;
        const label = input.parentElement.children[0].innerText;
        const displayErrors = (inputName == "address_city_origin" || inputName == "address_city") ? input.parentElement.children[3] : input.parentElement.children[2];
        const onlyNumber = inputValue.replace(/[^\d]+/g, '');
        let newValue = "";
    
        displayErrors.innerText = "";
    
        switch (inputName) {
            case "full_name":
                const fullNameSplit = inputValue.split(" ");
                if (inputValue.length >= 100) {
                    input.value = inputValue.toString().substr(0,99);
                    displayErrors.innerText = "Limite de 100 caracteres";
                } else if(fullNameSplit.length <= 1 || fullNameSplit[1].length < 3){
                    input.setAttribute("isValid", false);
                    displayErrors.innerText = "Digite o nome completo";
                }
                break;
    
            case "birth_date":
            case "qualification_started[]":
            case "qualification_end[]":
                const birthDateSplit = inputValue.split("-");
                if(birthDateSplit == ""){
                    input.setAttribute("isValid", false);
                    displayErrors.innerText = "Digite uma data v??lida";
                } else if(inputName == 'birth_date') {
                    const dob = new Date(inputValue);
                    const month_diff = Date.now() - dob.getTime();
                    const age_dt = new Date(month_diff); 
                    const year = age_dt.getUTCFullYear();
                    const age = Math.abs(year - 1970);

                    if(age < 100 && age > 0) {
                        document.getElementById("teacher_age").value = `${age} Anos`;
                    } else {
                        document.getElementById("teacher_age").value = "";
                    }
                }
                break;
    
            case "number_rg":
                if(onlyNumber.length < 9){
                    input.setAttribute("isValid", false);
                    displayErrors.innerText = "Digite um n??mero de RG v??lido";
                }
                const rgMask = {
                    1: "."
                    ,4: "."
                    ,7: "-"
                }
    
                for(const number in onlyNumber) {
                    if(number > 8) break
                    newValue += onlyNumber[number].toString();
                    newValue += rgMask[number] ?? "";
                }
                input.value = newValue;
                break;
    
            case "number_cpf":
                if(onlyNumber.length < 11) {
                    input.setAttribute("isValid", false);
                    displayErrors.innerText = "Digite um n??mero de CPF v??lido";
                }
    
                const cpfMask = {
                    2: "."
                    ,5: "."
                    ,8: "-"
                }
    
                for(const number in onlyNumber) {
                    if(number > 10) break
                    newValue += onlyNumber[number].toString();
                    newValue += cpfMask[number] ?? "";
                }
                input.value = newValue;
                break;
    
            case "email_address": 
                const regex = /\S+@\S+\.\S+/;
                if (inputValue.length >= 100) {
                    input.value = inputValue.toString().substr(0,99);
                    displayErrors.innerText = "Limite de 100 caracteres";
                } else if(!regex.test(inputValue)){
                    input.setAttribute("isValid", false);
                    displayErrors.innerText = "Digite um e-mail v??lido";
                };
                break;
    
            case "phone_number":
                    const cellPhoneMask = {
                        0: "("
                        ,2: ") "
                        ,7: "-"
                    }
                    if(onlyNumber.length < 11) {
                        input.setAttribute("isValid", false);
                        displayErrors.innerText = "Digite um n??mero de celular v??lido";
                    }
                    
                    for(const number in onlyNumber) {
                        if(number > 10) break
                        newValue += cellPhoneMask[number] ?? "";
                        newValue += onlyNumber[number].toString();
                    }
                    input.value = newValue;
                break;
    
            case "address_cep":
                if(onlyNumber.length < 8) {
                    input.setAttribute("isValid", false);
                    displayErrors.innerText = "Digite um CEP v??lido";
                }
    
                const cepMask = {
                    4: "-"
                }
    
                for(const number in onlyNumber) {
                    if(number > 7) break
                    newValue += onlyNumber[number].toString();
                    newValue += cepMask[number] ?? "";
                }
                input.value = newValue;
                break;
    
            case "address_number":
                if (inputValue.length > 6) {
                    input.value = inputValue.toString().substr(0,6);
                    displayErrors.innerText = "Limite de 6 caracteres";
                } else  if(!(inputValue.length > 0) || !(Number(onlyNumber) > 0)) {
                    input.setAttribute("isValid", false);
                    displayErrors.innerText = "Digite um n??mero residencial v??lido";
                }
    
                break;
    
            case "qualification_concluded":
                if( inputValue != "0" && inputValue != "1" ) {
                    input.setAttribute("isValid", false);
                    displayErrors.innerText = "Selecione o estado atual do curso.";
                }
    
                input.value = inputValue;
                break;
    
            case "qualification_level":
                if( !inputValue ) {
                    input.setAttribute("isValid", false);
                    displayErrors.innerText = "Selecione o n??vel do curso.";
                }
    
                input.value = inputValue;
                break;
    
            default:
                if(inputValue.length < 3) {
                    input.setAttribute("isValid", false);
                    displayErrors.innerText = `Digite ${label} v??lido`;
                }
                break;
        }
    } catch (error) { }

}

handlerVerifyRequiredFiels = () => {
    const inputsRequired = document.querySelectorAll("#form_data_teacher [isValid='false']");
    if(inputsRequired.length > 0) {
        for(const element in inputsRequired){
            try {
                inputsRequired[element].style.border = "1px solid var(--red-500)";
                handleValidateFormInput(inputsRequired[element])
            } catch (error) {}
        }
        
        const inputsValid = document.querySelectorAll("#form_data_teacher [isValid='true']");
        for(const element in inputsValid){
            try {
                inputsValid[element].style.border = "1px solid black";
            } catch (error) {}
        }

        return true;
    }

    return false;
}

handleSetFormDataList = ({levels, cities}) => {
    qualificationLevelListOption = "";

    const qualificationLevel = document.getElementById("qualification_level");
    qualificationLevel.innerHTML = `<option selected="selected" value="">Selecione uma op????o...</option>`;
    levels.forEach(element => {
        const option = document.createElement("option");
        option.value = element.id;
        option.innerText = element.name;
        qualificationLevel.append(option);
        qualificationLevelListOption += option.outerHTML;
    });

    const address_city_origin = document.getElementById("list_address_city_origin");
    const address_city = document.getElementById("list_address_city");
    address_city_origin.innerHTML = "";
    address_city.innerHTML = "";
    cities.forEach(element => {
        const option = document.createElement("option");
        option.setAttribute("ref", element.id);
        option.value = element.name;
        address_city.append(option.cloneNode(true));
        address_city_origin.append(option.cloneNode(true));
    });
};
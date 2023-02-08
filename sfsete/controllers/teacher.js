document.addEventListener("DOMContentLoaded", event => {
    fetch("/data/teachers/", {
        method: "GET",
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
    })
    .then(response => response.json())
    .then( ({error, msg}) => {

        if(error){
            throw new Error(msg);
        }

        handlerSetRowData(msg);
    }).catch( error => {
        document.getElementById("without-data").classList.remove("hidden-element");
    }).finally( () => {
        document.getElementById("loading-teacher-grid").classList.add("hidden-element");
    });
});

const formDataTeacher = document.getElementById("form_data_teacher");

handleGetFormDataList = async () => {

    const body = new FormData();
    body.append("action", "getFormDataInfo");
    await fetch("/operations/teachers/", {
        method: "POST",
        body: body
    })
    .then(response => response.json())
    .then( async ({error, msg}) => {

        if(error){
            throw new Error(msg);
        }

        await handleSetFormDataList(msg);
    }).catch( error => {
    });
}

handleformDataTeacherSubmited = (event) => {
    event.preventDefault();

    if(handlerVerifyRequiredFiels()){
        messagePopup("Alguns campos não são inválidos, por favor, verifique os campos em vermelho!");
        return;
    } else if(!document.querySelector("#form_data_teacher [name='gender']:checked")){
        handlerTabBoxActive(document.getElementById("link-tabs-box-1"));
        messagePopup("Selecione o gênero!");
        return;
    }

    const qualificationsConcludedChecked = document.querySelectorAll("#form_data_teacher .qualification-concluded:checked");
    const qualificationsConcludedContainer = document.querySelectorAll("#form_data_teacher .qualification-concluded-container");
    if(qualificationsConcludedChecked.length < qualificationsConcludedContainer.length){
        handlerTabBoxActive(document.getElementById("link-tabs-box-3"));
        messagePopup(`Verifique o campo "Estado atual do curso"!`);
        return;
    }

    const action = document.getElementById("form_action").value;
    const btns = document.querySelectorAll("#form_data_teacher button");
    btns.forEach( element => {
        element.setAttribute("disabled", true);
        if(element.getAttribute("type") == "submit"){
            element.innerHTML = '<i class="fa fa-sync fa-spin"></i> Salvando';
        }
    });

    const data = new FormData(formDataTeacher);
    var address_city =  document.getElementById("address_city").value;
    var address_city_origin =  document.getElementById("address_city_origin").value;

    address_city = address_city.split(" ").reduce( (prev, curr) => prev += `${curr[0].toUpperCase()}${curr.slice(1).toLowerCase()} `, "" ).trim();
    address_city_origin = address_city_origin.split(" ").reduce( (prev, curr) => prev += `${curr[0].toUpperCase()}${curr.slice(1).toLowerCase()} `, "" ).trim();

    address_city = document.querySelector(`#list_address_city option[value="${address_city}"]`)?.value ?
        document.querySelector(`#list_address_city option[value="${address_city}"]`).getAttribute("ref") : address_city;
    address_city_origin = document.querySelector(`#list_address_city_origin option[value="${address_city_origin}"]`)?.value ?
        document.querySelector(`#list_address_city_origin option[value="${address_city_origin}"]`).getAttribute("ref") : address_city_origin;

    data.set("address_city", address_city);
    data.set("address_city_origin", address_city_origin);
    fetch("/operations/teachers/", {
        method: "POST",
        body: data
    })
    .then(response => response.json())
    .then( ({error, msg, json}) => {

        if(error){
            throw new Error(msg);
        }

        if(typeof json == "object") {
            handlerUpdateRowData(json, action);
        }

        const modalAddTeacher = document.getElementById("modalAddTeacher");
        handleHideModal(modalAddTeacher);
        messagePopup(msg);
    }).catch( error => {
        messagePopup(`${error}`);
    }).finally( () => {
        btns.forEach( element => {
            element.removeAttribute("disabled");
            if(element.getAttribute("type") == "submit"){
                element.innerHTML = 'Salvar';
            }
        });
    });
}
formDataTeacher.addEventListener("submit", handleformDataTeacherSubmited);


handleGetToEdit = (rowData) => {
    const ref = rowData.getAttribute("ref");
    document.getElementById("form_ref").value = ref;

    const action = "getToEdit";
    document.getElementById("form_action").value = action;

    const body = new FormData(formDataTeacher);
    fetch("/operations/teachers/", {
        method: "POST",
        body: body
    })
    .then(response => response.json())
    .then( async ({error, msg}) => {

        if(error){
            throw new Error(msg);
        }

        handleResetModal();
        await handleShowModal(modalAddTeacher);
        handlerPutValuesFields(msg);
        document.getElementById("form_ref").value = ref;
        document.getElementById("form_action").value = "edit";
        handlerVerifyRequiredFiels();
    }).catch( error => {
        messagePopup(`Atenção: Erro ao buscar informações!`);
    });
}

deleteData = () => {

    const btns = document.querySelectorAll("#form_data_teacher button");
    btns.forEach( element => {
        element.setAttribute("disabled", true);
        if(element.classList.contains("delete-data")){
            element.innerHTML = '<i class="fa fa-sync fa-spin"></i> Excluindo';
        }
    });

    const action = "delete";
    document.getElementById("form_action").value = action;
    const body = new FormData(formDataTeacher);
    fetch("/operations/teachers/", {
        method: "POST",
        body: body
    })
    .then( response => response.json() )
    .then( ({error, msg}) => {

        if(error){
            throw new Error(msg);
        }

        btns.forEach( element => {
            element.setAttribute("disabled", true);
            if(element.classList.contains("delete-data")){
                element.innerHTML = '<i class="fa-solid fa-trash"></i> Excluir';
            }
        });

        const modalAddTeacher = document.getElementById("modalAddTeacher");
        const ref = document.getElementById("form_ref");
        handlerUpdateRowData(ref, action);
        handleHideModal(modalAddTeacher);
        messagePopup(msg);
    }).catch( error => {
        btns.forEach( element => {
            element.setAttribute("disabled", true);
            if(element.classList.contains("delete-data")){
                element.innerHTML = '<i class="fa-solid fa-trash"></i> Excluir';
            }
        });
        messagePopup(`Atenção: Erro ao excluir docente!`);
    });

}

handleDeleteFileQualification = (ref, file, index, path) => {

    const action = "deleteFileQualification";
    const body = new FormData();
    body.append("action", action);
    body.append("ref", ref);
    body.append("path", path);
    fetch("/operations/teachers/", {
        method: "POST",
        body: body
    })
    .then( response => response.json() )
    .then( ({error, msg}) => {

        if(error){
            throw new Error(msg);
        }

        file.parentElement.innerHTML = `<input type="file" name="qualification_document[${index}]" id="qualification_document${index}">`;
        messagePopup(msg);
    }).catch( error => {
        file.classList.add("fa-solid", "fa-trash");
        file.classList.remove("fa", "fa-sync", "fa-spin");
        file.style.pointerEvents = "auto";
        messagePopup(`Atenção: Erro ao excluir qualificação!`);
    });

}
# Teste CF7

CRUD de cadastro de docentes com possibilidade de anexar certificados em pdf.

Aplicação construída sem nenhum plugin ou framework.

---

## Preparando o ambiente

Segue um passo a passo inicial.

Obs.: Você vai precisar ter o Docker instalado na sua máquina.

1. Clone este **repositório**.
2. Com o Docker em execução, execute os containers *Docker* com o comando ```docker-compose up```  no seu terminal, este comando deve ser executado na pasta onde estiver clonado o projeto.
3. Aguarde enquanto o docker constrói as imagens e executa os containers.
4. Se tudo correr bem, acesse *localhost:8888* no seu navegador para executar aplicação.
5. Se precisar acessar o ADMINER, acesse *localhost:8080*. Sistema: MySQL, Servidor: container_db, Usuário: root, Senha: SFSete2023!

---

## Comandos para criar a estrutura da base de dados

1. Crie a base de dados;

```mysql
CREATE DATABASE if not exists db_md_sfsete;
```

2. Selecione a base de dados db_md_sfsete;
3. Execute os comandos abaixo para criar as tabelas;

```mysql
CREATE TABLE t_cities (
    id bigint not null auto_increment,
    name varchar(100) not null,

    flg_active bit default 1,
    created_at datetime not null,
    updated_at datetime null,
    deleted_at datetime null,

    primary key (id)
);

INSERT INTO t_cities
(name, created_at)
VALUES
('Jandira', NOW()),
('Osasco', NOW()),
('Itapevi', NOW()),
('Barueri', NOW()),
('São Paulo', NOW()),
('Cotia', NOW());
```

```mysql
CREATE TABLE t_levels (
    id bigint not null auto_increment,
    name varchar(100) not null,

    flg_active bit default 1,
    created_at datetime not null,
    updated_at datetime null,
    deleted_at datetime null,

    primary key (id)
);

INSERT INTO t_levels 
(name, created_at)
VALUES
('Curso Técnico', NOW()),
('Ensino Superior', NOW()),
('Pós-graduação - Especialização/MBA', NOW()),
('Pós-graduação - Mestrado', NOW()),
('Pós-graduação - Doutorado', NOW());
```

```mysql
CREATE TABLE t_teachers (
    id bigint not null auto_increment,
    name varchar(100) not null,
    birth_date date not null,
    rg_number varchar(14) not null,
    cpf_number varchar(14) not null,
    email_address varchar(100) not null,
    phone varchar(50) not null,
    gender varchar(10) not null,

    address varchar(100) not null,
    address_number varchar(6) not null,
    address_district varchar(50) not null,
    address_city varchar(50) not null,
    address_state varchar(50) not null,
    address_cep varchar(50) not null,
    address_city_origin varchar(50) not null,

    flg_active bit default 1,
    created_at datetime not null,
    updated_at datetime null,
    deleted_at datetime null,
    primary key (id)
);

ALTER TABLE t_teachers
MODIFY COLUMN address_city bigint not null,
MODIFY COLUMN address_city_origin bigint not null,
ADD CONSTRAINT fk_cities
foreign key (address_city) references t_cities(id),
ADD CONSTRAINT fk_cities_origin
foreign key (address_city_origin) references t_cities(id);

ALTER TABLE t_teachers
CHANGE `address_city` `address_city_id` bigint NOT NULL AFTER `address_district`,
CHANGE `address_city_origin` `address_city_origin_id` bigint NOT NULL AFTER `address_city_id`;
```

```mysql
CREATE TABLE t_qualifications (
    id bigint not null auto_increment,
    teacher_id bigint not null,
    name varchar(100) not null,
    level varchar(100) not null,
    institution_name varchar(100) not null,
    country varchar(50) not null,
    state varchar(50) not null,
    started_at datetime null,
    end_at datetime null,
    flg_concluded bit null,
    document_file_path varchar(100) null,

    flg_active bit default 1,
    created_at datetime not null,
    updated_at datetime null,
    deleted_at datetime null,

    foreign key (teacher_id)
        references t_teachers(id),

    primary key (id)
);

ALTER TABLE t_qualifications
MODIFY COLUMN level bigint not null,
ADD CONSTRAINT fk_levels
foreign key (level) references t_levels(id);

ALTER TABLE t_qualifications
CHANGE `level` `level_id` bigint NOT NULL AFTER `name`;
```

---
# TaskBoardHQ

A team‑focused task management GraphQL API built with Laravel 10, Lighthouse GraphQL, MySQL/Postgres, and JWT authentication.

This document is the project’s single source of truth. It defines the full scope: features, data model, GraphQL surface, authorization rules, non‑functional requirements, development plan, testing, and deployment notes.

---

## 1. Overview

**Project name:** TaskBoardHQ
**Tech stack:** Laravel 10, Lighthouse GraphQL, PHP 8.4, MySQL/Postgres, Redis (optional), php‑open‑source‑saver/jwt‑auth
**Repository:** `taskboardhq`
**Goal:** Build a clean, production‑ready GraphQL backend supporting projects, tasks, users, comments, and activity logs.

---

## 2. Features (MVP)

* JWT authentication (register, login)
* User profiles
* Organizations / Projects
* Tasks (CRUD, assign, change status)
* Comments
* Activity log (polymorphic)
* Pagination, filtering, sorting
* Authorization policies
* N+1 mitigation with DataLoader
* Basic rate limiting & validation
* GraphQL Playground

---

## 3. Data Model

### users

* id BIGINT PK
* name VARCHAR(191)
* email VARCHAR(191) unique
* password VARCHAR(255)
* avatar_url VARCHAR(255) nullable
* role ENUM(member, manager, admin) default member
* timestamps

**Indexes:** email unique

### projects

* id BIGINT PK
* name VARCHAR(191)
* slug VARCHAR(191) unique
* description TEXT nullable
* owner_id BIGINT FK users.id
* visibility ENUM(private, public) default private
* timestamps

**Indexes:** owner_id, slug unique

### project_user (pivot)

* id BIGINT PK
* project_id BIGINT FK projects.id
* user_id BIGINT FK users.id
* role ENUM(member, maintainer) default member
* joined_at timestamp

**Indexes:** composite (project_id, user_id) unique

### tasks

* id BIGINT PK
* project_id BIGINT FK
* title VARCHAR(255)
* description TEXT nullable
* status ENUM(todo, in_progress, done, archived) default todo
* priority ENUM(low, medium, high) default medium
* assignee_id BIGINT FK users.id nullable
* reporter_id BIGINT FK users.id
* due_date DATE nullable
* estimated_hours DECIMAL(5,2) nullable
* timestamps

**Indexes:** project_id, assignee_id, status

### comments

* id BIGINT PK
* task_id BIGINT FK tasks.id
* user_id BIGINT FK users.id
* body TEXT
* timestamps

**Indexes:** task_id, user_id

### activity_logs (polymorphic)

* id BIGINT PK
* causer_id BIGINT FK users.id nullable
* loggable_type VARCHAR(255)
* loggable_id BIGINT
* action VARCHAR(64)
* meta JSON nullable
* created_at timestamp

**Indexes:** (loggable_type, loggable_id), causer_id

---

## 4. GraphQL Schema Overview

### Types

* User
* Project
* Task
* Comment
* ActivityLog

### Core Queries

* me
* users
* projects
* project(id / slug)
* tasks
* task(id)
* activityLogs(loggableType, loggableId)

### Core Mutations

* register
* login
* createProject / updateProject
* createTask / updateTask / assignTask
* commentTask

Inputs follow standard `Input` types. Auth returns `AuthPayload { token, user }`.

---

## 5. Authorization Rules

* Project owner and maintainers manage project and tasks.
* Members create tasks within joined projects.
* Users may update tasks they created or are assigned to.
* Admin/Manager roles may access global admin‑level actions.
* All enforced through Laravel Policies.

---

## 6. Non‑Functional Requirements

* Pagination on all list queries
* DataLoader for batching
* JWT with strict guards
* Rate limiting middleware
* Redis for caching (optional)
* Unit, integration, and E2E tests
* Structured logging

---

## 7. Directory Structure

```
app/
  GraphQL/
    Queries/
    Mutations/
      Auth/
      Project/
      Task/
      Comment/
    Types/
  Models/
  Services/
    DataLoader/
  Policies/
config/
  lighthouse.php
  jwt.php
graphql/
  schema.graphql
database/
  migrations/
  seeders/
routes/
  api.php
```

---

## 8. Development Plan (6–8 days)

### Day 0

* Confirm schema, DB, env
* Install Lighthouse, JWT, Redis

### Day 1 – Models & Migrations

* Create migrations for all tables
* Implement Eloquent relationships
* Add seeders

### Day 2 – Auth

* JWT register/login
* me query
* Basic user/project/task queries

### Day 3 – Project & Task CRUD

* Create/update Project
* Create/update/assign Task
* Validation + policies

### Day 4 – Comments + Activity Logs

* Comment creation/listing
* Activity model events
* Pagination

### Day 5 – N+1 fixes

* DataLoader setup
* Replace all heavy relations with loaders

### Day 6 – Testing & Polishing

* Add unit + GraphQL tests
* Finalize schema docs
* Write usage examples

### Optional Day 7 – Deployment

* Docker (optional)
* Redis + queue worker
* CI pipeline

---

## 9. Testing Plan

* Unit tests for models and policies
* Integration tests for GraphQL
* Manual QA flows

---

## 10. Deliverables

* Migrations + seeders
* GraphQL schema
* Auth with JWT
* CRUD endpoints
* Comments + Activity Log
* Pagination + filtering
* DataLoader
* Tests
* README

---

## 11. Environment Requirements

**Local:** PHP 8.4, Composer, MySQL/Postgres, Redis optional.

**Production:** Linux, Nginx, PHP‑FPM, Redis, Supervisor, HTTPS.

---

## 12. Risks

* Complex role policies
* Heavy nested queries
* Cache invalidation

---

## 13. Next Steps

1. Configure `.env` and run migrations.
2. Install jwt‑auth and generate secret.
3. Sync schema.graphql.
4. Create models and migrations if missing.

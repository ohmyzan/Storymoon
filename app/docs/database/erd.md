# Database Architecture Documentation

## Overview

This documentation covers the Entity Relationship Diagram (ERD) and Logical Record Structure (LRS) for the Storymoon platform. The architecture is optimized for high scalability using a combination of:

- **Increment ID**: For master tables
- **ULID**: For large-scale transactional tables

## Table of Contents

1. [Entity Relationship Diagram](#entity-relationship-diagram-erd)
2. [Logical Record Structure](#logical-record-structure-lrs)

---

## Entity Relationship Diagram (ERD)

The following diagram visualizes the relationships between entities in the Storymoon platform:

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string pen_name UK
        string email UK
        string password
        text google2fa_secret
        timestamp muted_until
        timestamp suspended_until
        timestamp banned_at
        timestamp author_verified_at
        timestamp editor_verified_at
        timestamp deleted_at
    }

    WALLETS {
        bigint id PK
        bigint user_id FK
        unsigned_int coin_balance
        decimal revenue_balance
    }

    NOVELS {
        ulid id PK
        bigint author_id FK
        bigint editor_id FK
        string title
        string slug UK
        text synopsis
        string cover_image
        enum status
        unsigned_int views_count
        unsigned_int favorites_count
        unsigned_int total_chapters
        decimal rating
        boolean is_frozen
        timestamp deleted_at
    }

    CHAPTERS {
        ulid id PK
        ulid novel_id FK
        string title
        string slug
        longtext content
        unsigned_int word_count
        int chapter_number
        text editor_notes
        enum status
        timestamp published_at
        boolean is_premium
        int coin_price
        timestamp deleted_at
    }

    CONTRACTS {
        ulid id PK
        ulid novel_id FK
        bigint author_id FK
        bigint editor_id FK
        enum contract_type
        int revenue_share_author
        int revenue_share_platform
        string real_name
        string id_card_number
        string id_card_image
        string selfie_image
        string bank_name
        string bank_account_number
        string bank_account_name
        text signature_image
        string contract_document_path
        enum status
        text editor_notes
        timestamp signed_at
        timestamp deleted_at
    }

    TRANSACTIONS {
        ulid id PK
        bigint user_id FK
        bigint wallet_id FK
        enum type
        int coin_amount
        decimal rupiah_amount
        enum status
        string reference_id UK
        json meta_data
        timestamp deleted_at
    }

    CHAPTER_PURCHASES {
        ulid id PK
        bigint reader_id FK
        ulid chapter_id FK
        bigint author_id FK
        int price_coined
        int author_earning
        int platform_earning
        enum contract_type_snapshot
        int revenue_share_snapshot
    }

    PAYOUT_STATEMENTS {
        ulid id PK
        bigint author_id FK
        int month
        int year
        int total_gross_coins
        int platform_fee_coins
        int tax_coins
        int net_author_coins
        enum status
    }

    TOP_UPS {
        ulid id PK
        bigint user_id FK
        string reference_id UK
        unsigned_int amount_rupiah
        unsigned_int coins_granted
        string payment_method
        enum status
        timestamp settled_at
    }

    WITHDRAWALS {
        ulid id PK
        bigint user_id FK
        unsigned_int coins_redeemed
        unsigned_int amount_rupiah
        string bank_name
        string bank_account_number
        string bank_account_name
        string proof_image
        bigint processed_by FK
        enum status
        text finance_notes
    }

    REVIEWS {
        ulid id PK
        bigint user_id FK
        ulid novel_id FK
        tinyint rating
        text content
        timestamp deleted_at
    }

    COMMENTS {
        ulid id PK
        bigint user_id FK
        ulid novel_id FK
        ulid chapter_id FK
        string paragraph_id
        text content
        boolean is_hidden
        timestamp deleted_at
    }

    USERS ||--|| WALLETS : "has_one"
    USERS ||--o{ NOVELS : "writes"
    USERS ||--o{ TRANSACTIONS : "performs"
    USERS ||--o{ TOP_UPS : "requests"
    USERS ||--o{ WITHDRAWALS : "claims"

    WALLETS ||--o{ TRANSACTIONS : "records"

    NOVELS ||--o{ CHAPTERS : "contains"
    NOVELS ||--|| CONTRACTS : "binds"
    NOVELS ||--o{ REVIEWS : "receives"
    NOVELS ||--o{ COMMENTS : "discusses"

    CHAPTERS ||--o{ CHAPTER_PURCHASES : "sold_in"
    CHAPTERS ||--o{ COMMENTS : "annotated_in"

    USERS ||--o{ CHAPTER_PURCHASES : "buys_as_reader"
    USERS ||--o{ CHAPTER_PURCHASES : "earns_as_author"
    USERS ||--o{ PAYOUT_STATEMENTS : "receives_monthly"
    USERS ||--o{ REVIEWS : "writes_review"
    USERS ||--o{ COMMENTS : "writes_comment"
```

---

## Logical Record Structure (LRS)

The LRS maps the data structure above into Foreign Key (FK) constraints that enforce business logic integrity rules:

### Core Entity Structures

#### Users

```
users (id [PK], name, pen_name, email, ...)
```

#### Wallets

```
wallets (id [PK], user_id [FK -> users.id - UNIQUE], coin_balance, revenue_balance)
```

#### Novels

```
novels (id [PK], author_id [FK -> users.id], editor_id [FK -> users.id - NULLABLE], title, slug [UNIQUE], ...)
```

#### Chapters

```
chapters (id [PK], novel_id [FK -> novels.id], title, slug, chapter_number, ... [UNIQUE composite: novel_id + slug])
```

#### Contracts

```
contracts (id [PK], novel_id [FK -> novels.id], author_id [FK -> users.id], editor_id [FK -> users.id - NULLABLE], ... [UNIQUE composite: novel_id + status as active contract guard])
```

#### Transactions

```
transactions (id [PK], user_id [FK -> users.id], wallet_id [FK -> wallets.id], reference_id [UNIQUE], coin_amount, rupiah_amount, ...)
```

#### Chapter Purchases

```
chapter_purchases (id [PK], reader_id [FK -> users.id], chapter_id [FK -> chapters.id], author_id [FK -> users.id], ...)
```

#### Payout Statements

```
payout_statements (id [PK], author_id [FK -> users.id], ... [UNIQUE composite: author_id + month + year to prevent duplicate statements])
```

#### Top-ups

```
top_ups (id [PK], user_id [FK -> users.id], reference_id [UNIQUE], ...)
```

#### Withdrawals

```
withdrawals (id [PK], user_id [FK -> users.id], processed_by [FK -> users.id - NULLABLE], ...)
```

#### Reviews

```
reviews (id [PK], user_id [FK -> users.id], novel_id [FK -> novels.id], ... [UNIQUE composite: user_id + novel_id to prevent review bombing])
```

#### Comments

```
comments (id [PK], user_id [FK -> users.id], novel_id [FK -> novels.id], chapter_id [FK -> chapters.id - NULLABLE], ...)
```

```

```

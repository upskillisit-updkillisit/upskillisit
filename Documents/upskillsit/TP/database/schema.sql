CREATE DATABASE IF NOT EXISTS `upskillsit` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `upskillsit`;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `admin_logs`,`contact_messages`,`password_resets`,`notifications`,`articles`,`registrations`,`events`,`resources`,`applications`,`opportunities`,`experiences`,`projects`,`student_skills`,`skills`,`company_profiles`,`professional_profiles`,`student_profiles`,`users`;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('student','professional','company','admin') NOT NULL DEFAULT 'student',
  status ENUM('active','suspended') NOT NULL DEFAULT 'active',
  terms_accepted TINYINT(1) NOT NULL DEFAULT 0,
  data_consent TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_role_status (role,status)
) ENGINE=InnoDB;

CREATE TABLE password_resets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_password_resets_expiry (expires_at),
  CONSTRAINT fk_password_reset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE student_profiles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL UNIQUE,
  photo_path VARCHAR(255), cv_path VARCHAR(255),
  city VARCHAR(120), country VARCHAR(120), school VARCHAR(190), study_level VARCHAR(120), major VARCHAR(150), specialty VARCHAR(150),
  degree VARCHAR(150), study_year VARCHAR(80), certifications TEXT, extra_training TEXT,
  programming_languages TEXT, frameworks TEXT, `databases` TEXT, cloud TEXT, cybersecurity TEXT, data_ai TEXT, networks TEXT, systems TEXT, tools TEXT, other_skills TEXT, bio TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_student_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE professional_profiles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL UNIQUE,
  function_title VARCHAR(150), company VARCHAR(190), expertise_domain VARCHAR(150), experience_years INT UNSIGNED,
  skills TEXT, location VARCHAR(150), career_path TEXT, interests TEXT, public_links TEXT, bio TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_professional_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE company_profiles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL UNIQUE,
  company_name VARCHAR(190), sector VARCHAR(150), website VARCHAR(255), location VARCHAR(150),
  contact_person VARCHAR(150), description TEXT, hiring_needs TEXT, public_links TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_company_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE skills (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  category VARCHAR(120) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE student_skills (
  student_profile_id BIGINT UNSIGNED NOT NULL,
  skill_id BIGINT UNSIGNED NOT NULL,
  level ENUM('debutant','intermediaire','avance') DEFAULT 'intermediaire',
  PRIMARY KEY(student_profile_id,skill_id),
  FOREIGN KEY(student_profile_id) REFERENCES student_profiles(id) ON DELETE CASCADE,
  FOREIGN KEY(skill_id) REFERENCES skills(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE projects (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(190) NOT NULL,
  description TEXT,
  technologies VARCHAR(255),
  role VARCHAR(150),
  public_link VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_projects_user (user_id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE experiences (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  type VARCHAR(120),
  title VARCHAR(190) NOT NULL,
  organization VARCHAR(190),
  description TEXT,
  started_at DATE,
  ended_at DATE,
  INDEX idx_experiences_user (user_id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE opportunities (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  type VARCHAR(80) NOT NULL,
  organism VARCHAR(190) NOT NULL,
  description TEXT NOT NULL,
  target_profile TEXT,
  skills TEXT,
  location VARCHAR(150),
  deadline DATE,
  application_method TEXT,
  application_link VARCHAR(255),
  company_user_id BIGINT UNSIGNED NULL,
  status ENUM('draft','published','closed') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_opportunities_status_deadline (status,deadline),
  INDEX idx_opportunities_type_location (type,location),
  INDEX idx_opportunities_company (company_user_id),
  FOREIGN KEY(company_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE applications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  opportunity_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  message TEXT,
  status ENUM('new','reviewed','shortlisted','rejected') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_applications_opportunity_user (opportunity_id,user_id),
  INDEX idx_applications_status (status),
  FOREIGN KEY(opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE resources (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  description TEXT NOT NULL,
  category VARCHAR(120) NOT NULL,
  author VARCHAR(150),
  source VARCHAR(190),
  content LONGTEXT,
  link VARCHAR(255),
  document_path VARCHAR(255),
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_resources_status_category (status,category)
) ENGINE=InnoDB;

CREATE TABLE events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  description TEXT NOT NULL,
  event_date DATETIME NOT NULL,
  location VARCHAR(150),
  capacity INT UNSIGNED NOT NULL DEFAULT 1,
  speaker VARCHAR(150),
  status ENUM('draft','published','closed') NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_events_status_date (status,event_date)
) ENGINE=InnoDB;

CREATE TABLE registrations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  event_id BIGINT UNSIGNED NOT NULL,
  status ENUM('confirmed','waiting_list','cancelled') NOT NULL DEFAULT 'confirmed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_registration_user_event (user_id,event_id),
  INDEX idx_registrations_event_status (event_id,status),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(event_id) REFERENCES events(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE articles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  excerpt TEXT,
  content LONGTEXT,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(190) NOT NULL,
  body TEXT,
  read_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL,
  subject VARCHAR(190) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('new','read','archived') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_contact_messages_status (status),
  INDEX idx_contact_messages_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE admin_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  admin_id BIGINT UNSIGNED NULL,
  action VARCHAR(190) NOT NULL,
  details TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

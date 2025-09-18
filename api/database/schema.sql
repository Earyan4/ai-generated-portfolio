-- Create and use DB
IF DB_ID('portfolio_system') IS NULL CREATE DATABASE portfolio_system;
GO
USE portfolio_system;
GO

-- Users
IF OBJECT_ID('dbo.users','U') IS NOT NULL DROP TABLE dbo.users;
CREATE TABLE dbo.users (
  id INT IDENTITY(1,1) PRIMARY KEY,
  full_name NVARCHAR(255) NOT NULL,
  email NVARCHAR(255) NOT NULL UNIQUE,
  password NVARCHAR(255) NOT NULL,
  profession NVARCHAR(100) NOT NULL,
  phone NVARCHAR(20),
  location NVARCHAR(255),
  website NVARCHAR(255),
  profile_photo NVARCHAR(500),
  summary NVARCHAR(MAX),
  created_at DATETIME2 DEFAULT SYSUTCDATETIME(),
  updated_at DATETIME2 DEFAULT SYSUTCDATETIME()
);
GO

-- Skills
IF OBJECT_ID('dbo.skills','U') IS NOT NULL DROP TABLE dbo.skills;
CREATE TABLE dbo.skills (
  id INT IDENTITY(1,1) PRIMARY KEY,
  user_id INT NOT NULL,
  skill_name NVARCHAR(100) NOT NULL,
  skill_type NVARCHAR(10) NOT NULL
    CHECK (skill_type IN ('technical','soft','tools')),
  proficiency_level INT DEFAULT 50,
  CONSTRAINT FK_skills_users
    FOREIGN KEY (user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
);
GO

-- Experience
IF OBJECT_ID('dbo.experience','U') IS NOT NULL DROP TABLE dbo.experience;
CREATE TABLE dbo.experience (
  id INT IDENTITY(1,1) PRIMARY KEY,
  user_id INT NOT NULL,
  job_title NVARCHAR(255) NOT NULL,
  company NVARCHAR(255) NOT NULL,
  start_date DATE,
  end_date DATE,
  is_current BIT DEFAULT 0,
  description NVARCHAR(MAX),
  CONSTRAINT FK_experience_users
    FOREIGN KEY (user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
);
GO

-- Education
IF OBJECT_ID('dbo.education','U') IS NOT NULL DROP TABLE dbo.education;
CREATE TABLE dbo.education (
  id INT IDENTITY(1,1) PRIMARY KEY,
  user_id INT NOT NULL,
  degree NVARCHAR(255) NOT NULL,
  institution NVARCHAR(255) NOT NULL,
  start_date DATE,
  end_date DATE,
  grade NVARCHAR(50),
  location NVARCHAR(255),
  CONSTRAINT FK_education_users
    FOREIGN KEY (user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
);
GO

-- Projects
IF OBJECT_ID('dbo.projects','U') IS NOT NULL DROP TABLE dbo.projects;
CREATE TABLE dbo.projects (
  id INT IDENTITY(1,1) PRIMARY KEY,
  user_id INT NOT NULL,
  project_name NVARCHAR(255) NOT NULL,
  project_url NVARCHAR(500),
  technologies NVARCHAR(MAX),
  duration NVARCHAR(100),
  description NVARCHAR(MAX),
  project_image NVARCHAR(500),
  CONSTRAINT FK_projects_users
    FOREIGN KEY (user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
);
GO

-- Portfolio templates
IF OBJECT_ID('dbo.portfolio_templates','U') IS NOT NULL DROP TABLE dbo.portfolio_templates;
CREATE TABLE dbo.portfolio_templates (
  id INT IDENTITY(1,1) PRIMARY KEY,
  profession NVARCHAR(100) NOT NULL,
  template_name NVARCHAR(255) NOT NULL,
  template_data NVARCHAR(MAX) NOT NULL, -- JSON stored as NVARCHAR
  is_active BIT DEFAULT 1,
  created_at DATETIME2 DEFAULT SYSUTCDATETIME()
);
GO

-- User portfolios
IF OBJECT_ID('dbo.user_portfolios','U') IS NOT NULL DROP TABLE dbo.user_portfolios;
CREATE TABLE dbo.user_portfolios (
  id INT IDENTITY(1,1) PRIMARY KEY,
  user_id INT NOT NULL,
  template_id INT NOT NULL,
  custom_domain NVARCHAR(255),
  is_public BIT DEFAULT 1,
  custom_css NVARCHAR(MAX),
  custom_js NVARCHAR(MAX),
  created_at DATETIME2 DEFAULT SYSUTCDATETIME(),
  updated_at DATETIME2 DEFAULT SYSUTCDATETIME(),
  CONSTRAINT FK_user_portfolios_users
    FOREIGN KEY (user_id) REFERENCES dbo.users(id) ON DELETE CASCADE,
  CONSTRAINT FK_user_portfolios_templates
    FOREIGN KEY (template_id) REFERENCES dbo.portfolio_templates(id)
);
GO

-- Default templates
INSERT INTO dbo.portfolio_templates (profession, template_name, template_data) VALUES
(N'developer', N'Modern Developer', N'{"theme":"dark","layout":"modern","sections":["hero","about","skills","experience","projects","contact"]}'),
(N'doctor', N'Medical Professional', N'{"theme":"clean","layout":"medical","sections":["hero","about","education","experience","specializations","contact"]}'),
(N'photographer', N'Creative Photographer', N'{"theme":"minimal","layout":"gallery","sections":["hero","about","portfolio","services","testimonials","contact"]}'),
(N'video_editor', N'Video Editor', N'{"theme":"dark","layout":"video","sections":["hero","about","showreel","projects","skills","contact"]}'),
(N'marketing', N'Marketing Professional', N'{"theme":"corporate","layout":"business","sections":["hero","about","experience","case_studies","testimonials","contact"]}'),
(N'designer', N'Creative Designer', N'{"theme":"colorful","layout":"creative","sections":["hero","about","portfolio","skills","process","contact"]}'),
(N'writer', N'Content Writer', N'{"theme":"elegant","layout":"text","sections":["hero","about","writing_samples","experience","testimonials","contact"]}'),
(N'consultant', N'Business Consultant', N'{"theme":"professional","layout":"corporate","sections":["hero","about","services","experience","testimonials","contact"]}');
GO
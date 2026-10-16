USE `upskillsit`;

INSERT INTO users (first_name,last_name,email,password,role,terms_accepted,data_consent,status) VALUES
('Admin','UpSkillsIT','admin@example.com','$2y$10$9/IMdpCJ0N0BZgGH1qKxTO65rcTTThgol1DTlmU/wf.Rt1FmG1Byy','admin',1,1,'active'),
('Salma','Benali','student@example.com','$2y$10$AcXWnK8lI7oJudQU6Cr1WeMu8/Q4bzwGMdSFNTJjLWRno6I1/jwuK','student',1,1,'active'),
('Yassine','El Amrani','professional@example.com','$2y$10$Gfy0vnXtpDv.WbFZaQKRuu7Thmz28BSoNVgHab2wkIg6jOgNpPvI2','professional',1,1,'active'),
('Nadia','Recruiter','company@example.com','$2y$10$Gfy0vnXtpDv.WbFZaQKRuu7Thmz28BSoNVgHab2wkIg6jOgNpPvI2','company',1,1,'active'),
('Nora','Haddad','nora@example.com','$2y$10$AcXWnK8lI7oJudQU6Cr1WeMu8/Q4bzwGMdSFNTJjLWRno6I1/jwuK','student',1,1,'active'),
('Karim','Alaoui','karim@example.com','$2y$10$Gfy0vnXtpDv.WbFZaQKRuu7Thmz28BSoNVgHab2wkIg6jOgNpPvI2','professional',1,1,'active');

INSERT INTO student_profiles (user_id,city,country,school,study_level,major,specialty,degree,study_year,programming_languages,frameworks,`databases`,cloud,cybersecurity,data_ai,networks,systems,tools,bio) VALUES
(2,'Casablanca','Maroc','EST Casablanca','Bac+3','Genie informatique','Developpement web','Licence professionnelle','3e annee','PHP, JavaScript, Python','Laravel, React','MySQL, PostgreSQL','Docker, AWS basics','OWASP basics','Pandas, scikit-learn','TCP/IP basics','Linux','Git, GitHub, Figma','Etudiante passionnee par les plateformes web utiles.'),
(5,'Rabat','Maroc','ENSIAS','Bac+5','Data Engineering','IA','Master','2e annee','Python, Java','Spring, Flask','MySQL, MongoDB','Azure','Securite des donnees','Machine Learning','Reseaux campus','Linux','Git, Docker','Interessee par la data appliquee a l education.');

INSERT INTO professional_profiles (user_id,function_title,company,expertise_domain,experience_years,skills,location,career_path,interests,public_links,bio) VALUES
(3,'Lead Developer','Digital Factory Casablanca','Architecture web',8,'PHP, MySQL, JavaScript, Docker, CI/CD','Casablanca','Developpeur full-stack puis lead technique.','Mentorat, qualite logicielle, accessibilite','https://www.linkedin.com','Professionnel engage dans la transmission aux jeunes talents.'),
(6,'Consultant Cybersecurite','SecureMaghreb','Cybersecurite',6,'Pentest, ISO 27001, OWASP, Linux','Rabat','Audit, securite applicative et sensibilisation.','CTF, securite cloud','https://github.com','Consultant securite oriente pedagogie.');

INSERT INTO company_profiles (user_id,company_name,sector,website,location,contact_person,description,hiring_needs,public_links) VALUES
(4,'Atlas Digital','Services numeriques','https://example.com/atlas','Casablanca','Nadia Recruiter','Entreprise marocaine qui accompagne des clients dans la transformation digitale.','Stages PHP, profils data junior, alternance support cloud','https://www.linkedin.com/company/atlas-digital');

INSERT INTO skills (name,category) VALUES
('PHP','Langage'),('MySQL','Base de donnees'),('JavaScript','Langage'),('Docker','DevOps'),('GitHub','Outil'),('Cybersecurity','Securite'),('Data','IA'),('Cloud','Infrastructure');

INSERT INTO projects (user_id,name,description,technologies,role,public_link) VALUES
(2,'Plateforme de stages','Application MVC pour centraliser des offres de stage.','PHP, MySQL, JavaScript','Developpeuse backend','https://github.com/demo/stages'),
(2,'Portfolio IT','Portfolio responsive avec projets academiques.','HTML, CSS, JavaScript','UI et integration','https://github.com/demo/portfolio'),
(5,'Analyse CV','Prototype de scoring de CV pour etudiants.','Python, Flask','Data developer','https://github.com/demo/cv');

INSERT INTO experiences (user_id,type,title,organization,description,started_at,ended_at) VALUES
(2,'Stage','Assistante developpement web','Agence Web Casablanca','Maintenance de pages PHP et integration responsive.','2026-04-01','2026-06-30'),
(5,'Projet','Data challenge education','Universite Mohammed V','Preparation de donnees et dashboard de restitution.','2026-03-01','2026-04-15');

INSERT INTO opportunities (title,type,organism,description,target_profile,skills,location,deadline,application_method,application_link,status,published_at) VALUES
('Stage developpeur PHP/MySQL','Stage','Agence Web Casablanca','Participation a la maintenance et evolution de plateformes web professionnelles.','Bac+2/Bac+3 informatique','PHP, MySQL, JavaScript, Git','Casablanca','2026-12-20','CV + portfolio GitHub','https://example.com/apply/php','published',NOW()),
('Hackathon Data pour education','Hackathon','Universite Mohammed V','48 heures pour proposer des solutions data au service de orientation.','Etudiants data et web','Python, Data, UX','Rabat','2026-11-15','Inscription en ligne','https://example.com/hackathon','published',NOW()),
('Formation Docker pour debutants','Formation','Tech Community Maroc','Atelier pratique pour comprendre conteneurs, images et deploiement local.','Apprenants IT','Docker, Linux, Git','En ligne','2026-11-05','Formulaire communautaire','https://example.com/docker','published',NOW()),
('Concours cybersecurite junior','Concours','SecureMaghreb','Challenges CTF web et Linux pour profils juniors.','Etudiants securite','OWASP, Linux, Reseau','Casablanca','2026-12-01','Inscription equipe','https://example.com/ctf','published',NOW()),
('Projet academique CRM associatif','Projet','Association Jeunes Tech','Creer un mini CRM pour gerer adherents et evenements.','Etudiants web','PHP, MySQL, Bootstrap','Fes','2026-11-25','Contact email','https://example.com/crm','published',NOW()),
('Benevolat IT pour ateliers lycee','Benevolat','Coding For Morocco','Animer des ateliers initiation HTML/CSS.','Etudiants pedagogues','HTML, CSS, Communication','Marrakech','2026-12-10','CV court','https://example.com/volunteer','published',NOW()),
('Stage support cloud junior','Stage','CloudOps Maroc','Support et documentation sur environnements cloud.','Bac+3 systemes/reseaux','Cloud, Linux, Reseaux','Tanger','2026-12-30','CV + lettre','https://example.com/cloud','published',NOW());

INSERT INTO resources (title,description,category,author,source,content,link,status,published_at) VALUES
('Construire un CV IT lisible','Methode simple pour valoriser competences, projets et stages.','CV','UpSkillsIT','Guide interne','Un bon CV IT met en avant un titre clair, une pile technique maitrisee, des projets verifiables et des resultats concrets.','https://example.com/cv','published',NOW()),
('Optimiser son GitHub etudiant','Bonnes pratiques README, commits et portfolio technique.','GitHub','UpSkillsIT','Guide interne','Chaque projet doit expliquer le besoin, les technologies, installation et les captures importantes.','https://example.com/github','published',NOW()),
('Preparer un entretien technique','Questions frequentes et methode de reponse structuree.','Entretien','Mentors UpSkillsIT','Atelier mentorat','Revisez les bases, preparez vos projets et entrainez-vous a expliquer vos choix techniques.','https://example.com/interview','published',NOW()),
('LinkedIn pour jeunes diplomes IT','Creer une presence professionnelle claire.','LinkedIn','UpSkillsIT','Guide interne','Un profil LinkedIn efficace montre votre specialite, vos projets et votre disponibilite.','https://example.com/linkedin','published',NOW()),
('Veille technologique sans surcharge','Organiser sa veille en 30 minutes par semaine.','Veille technologique','Community Team','Newsletter','Choisissez peu de sources, notez vos apprentissages et testez regulierement.','https://example.com/veille','published',NOW()),
('Certifications utiles en cloud','Panorama des certifications cloud accessibles.','Certifications','Cloud Mentor','Webinaire','Commencez par les fondamentaux, puis specialisez-vous selon votre projet professionnel.','https://example.com/cloud-cert','published',NOW());

INSERT INTO events (title,description,event_date,location,capacity,speaker,status) VALUES
('Atelier CV et GitHub','Session pratique pour ameliorer son CV et son profil GitHub.','2026-11-12 18:00:00','Casablanca Technopark',35,'Salma El Idrissi','published'),
('Webinaire cybersecurite junior','Comprendre les premiers pas vers la securite applicative.','2026-11-20 19:00:00','En ligne',80,'Karim Alaoui','published'),
('Rencontre metiers Data','Discussion avec des data engineers et analystes.','2026-12-03 17:30:00','Rabat',50,'Leila Mansouri','published'),
('Bootcamp entretien technique','Simulations et feedback pour jeunes developpeurs.','2026-12-14 09:30:00','Marrakech',25,'Yassine El Amrani','published'),
('Forum stages IT Maroc','Forum de mise en relation etudiants-entreprises.','2027-01-18 10:00:00','Casablanca',120,'Equipe UpSkillsIT','published');

INSERT INTO registrations (user_id,event_id,status) VALUES
(2,1,'confirmed'),(5,1,'confirmed'),(2,2,'confirmed');

UPDATE opportunities SET company_user_id=4 WHERE id IN (1,7);

INSERT INTO applications (opportunity_id,user_id,message,status) VALUES
(1,2,'Je souhaite rejoindre ce stage pour renforcer mes competences PHP/MySQL et contribuer aux evolutions backend.','new'),
(1,5,'Profil data avec bases web, interesse par un environnement produit.','reviewed'),
(7,3,'Je peux accompagner le support cloud et la documentation technique.','shortlisted');

INSERT INTO articles (title,slug,excerpt,content,status,published_at) VALUES
('Pourquoi documenter ses projets IT','documenter-projets-it','Un projet bien documente inspire confiance aux recruteurs.','Contenu long de demonstration.','published',NOW()),
('Les competences PHP encore recherchees','competences-php','PHP reste central pour de nombreuses plateformes professionnelles.','Contenu long de demonstration.','published',NOW()),
('Reussir son premier stage developpeur','premier-stage-developpeur','Preparation, posture et communication font la difference.','Contenu long de demonstration.','published',NOW());

INSERT INTO notifications (user_id,title,body) VALUES
(2,'Bienvenue sur UpSkillsIT','Complete ton profil pour recevoir de meilleures recommandations.'),
(3,'Espace mentor disponible','Ajoute tes domaines expertise pour aider les etudiants.');

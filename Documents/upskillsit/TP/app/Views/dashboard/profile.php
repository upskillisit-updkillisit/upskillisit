<?php use App\Core\CSRF; $projects = array_pad($projects ?? [], 3, []); $experiences = array_pad($experiences ?? [], 3, []); ?>
<form class="form panel" method="post" enctype="multipart/form-data">
    <?= CSRF::field() ?>
    <h1>Mon profil</h1>

    <?php if ($user['role'] === 'professional'): ?>
        <div class="grid-2">
            <label>Fonction<input name="function_title" value="<?= e($profile['function_title'] ?? '') ?>"></label>
            <label>Entreprise<input name="company" value="<?= e($profile['company'] ?? '') ?>"></label>
            <label>Domaine d'expertise<input name="expertise_domain" value="<?= e($profile['expertise_domain'] ?? '') ?>"></label>
            <label>Annees d'experience<input type="number" min="0" name="experience_years" value="<?= e($profile['experience_years'] ?? '') ?>"></label>
            <label>Localisation<input name="location" value="<?= e($profile['location'] ?? '') ?>"></label>
            <label>Liens professionnels<input name="public_links" value="<?= e($profile['public_links'] ?? '') ?>"></label>
        </div>
        <label>Competences<textarea name="skills"><?= e($profile['skills'] ?? '') ?></textarea></label>
        <label>Parcours professionnel<textarea name="career_path"><?= e($profile['career_path'] ?? '') ?></textarea></label>
        <label>Interets professionnels<textarea name="interests"><?= e($profile['interests'] ?? '') ?></textarea></label>
        <label>Bio<textarea name="bio"><?= e($profile['bio'] ?? '') ?></textarea></label>

    <?php elseif ($user['role'] === 'company'): ?>
        <div class="grid-2">
            <label>Nom de l'entreprise<input name="company_name" value="<?= e($profile['company_name'] ?? '') ?>"></label>
            <label>Secteur<input name="sector" value="<?= e($profile['sector'] ?? '') ?>"></label>
            <label>Site web<input type="url" name="website" value="<?= e($profile['website'] ?? '') ?>"></label>
            <label>Localisation<input name="location" value="<?= e($profile['location'] ?? '') ?>"></label>
            <label>Contact recruteur<input name="contact_person" value="<?= e($profile['contact_person'] ?? '') ?>"></label>
            <label>Liens publics<input name="public_links" value="<?= e($profile['public_links'] ?? '') ?>"></label>
        </div>
        <label>Description<textarea name="description"><?= e($profile['description'] ?? '') ?></textarea></label>
        <label>Besoins de recrutement<textarea name="hiring_needs"><?= e($profile['hiring_needs'] ?? '') ?></textarea></label>

    <?php else: ?>
        <div class="grid-2">
            <label>Ville<input name="city" value="<?= e($profile['city'] ?? '') ?>"></label>
            <label>Pays<input name="country" value="<?= e($profile['country'] ?? '') ?>"></label>
            <label>Etablissement<input name="school" value="<?= e($profile['school'] ?? '') ?>"></label>
            <label>Niveau d'etudes<input name="study_level" value="<?= e($profile['study_level'] ?? '') ?>"></label>
            <label>Filiere<input name="major" value="<?= e($profile['major'] ?? '') ?>"></label>
            <label>Specialite<input name="specialty" value="<?= e($profile['specialty'] ?? '') ?>"></label>
            <label>Diplome<input name="degree" value="<?= e($profile['degree'] ?? '') ?>"></label>
            <label>Annee d'etude<input name="study_year" value="<?= e($profile['study_year'] ?? '') ?>"></label>
            <label>Photo<input type="file" name="photo" accept="image/jpeg,image/png,image/webp"></label>
            <label>CV PDF<input type="file" name="cv" accept="application/pdf"></label>
        </div>
        <label>Certifications<textarea name="certifications"><?= e($profile['certifications'] ?? '') ?></textarea></label>
        <label>Formations complementaires<textarea name="extra_training"><?= e($profile['extra_training'] ?? '') ?></textarea></label>
        <div class="grid-2">
            <label>Langages<input name="programming_languages" value="<?= e($profile['programming_languages'] ?? '') ?>"></label>
            <label>Frameworks<input name="frameworks" value="<?= e($profile['frameworks'] ?? '') ?>"></label>
            <label>Bases de donnees<input name="databases" value="<?= e($profile['databases'] ?? '') ?>"></label>
            <label>Cloud<input name="cloud" value="<?= e($profile['cloud'] ?? '') ?>"></label>
            <label>Cybersecurite<input name="cybersecurity" value="<?= e($profile['cybersecurity'] ?? '') ?>"></label>
            <label>Data & IA<input name="data_ai" value="<?= e($profile['data_ai'] ?? '') ?>"></label>
            <label>Reseaux<input name="networks" value="<?= e($profile['networks'] ?? '') ?>"></label>
            <label>Systemes<input name="systems" value="<?= e($profile['systems'] ?? '') ?>"></label>
        </div>
        <label>Outils<input name="tools" value="<?= e($profile['tools'] ?? '') ?>"></label>
        <label>Autres compétences<textarea name="other_skills"><?= e($profile['other_skills'] ?? '') ?></textarea></label>
        <label>Bio<textarea name="bio"><?= e($profile['bio'] ?? '') ?></textarea></label>

        <h2>Projets</h2>
        <?php foreach ($projects as $i => $project): ?>
            <div class="subform grid-2">
                <label>Nom du projet<input name="projects[<?= $i ?>][name]" value="<?= e($project['name'] ?? '') ?>"></label>
                <label>Technologies<input name="projects[<?= $i ?>][technologies]" value="<?= e($project['technologies'] ?? '') ?>"></label>
                <label>Role<input name="projects[<?= $i ?>][role]" value="<?= e($project['role'] ?? '') ?>"></label>
                <label>Lien GitHub / demo<input name="projects[<?= $i ?>][public_link]" value="<?= e($project['public_link'] ?? '') ?>"></label>
                <label class="span-2">Description<textarea name="projects[<?= $i ?>][description]"><?= e($project['description'] ?? '') ?></textarea></label>
            </div>
        <?php endforeach; ?>

        <h2>Experiences</h2>
        <?php foreach ($experiences as $i => $experience): ?>
            <div class="subform grid-2">
                <label>Type<input name="experiences[<?= $i ?>][type]" value="<?= e($experience['type'] ?? '') ?>"></label>
                <label>Titre<input name="experiences[<?= $i ?>][title]" value="<?= e($experience['title'] ?? '') ?>"></label>
                <label>Organisation<input name="experiences[<?= $i ?>][organization]" value="<?= e($experience['organization'] ?? '') ?>"></label>
                <label>Debut<input type="date" name="experiences[<?= $i ?>][started_at]" value="<?= e($experience['started_at'] ?? '') ?>"></label>
                <label>Fin<input type="date" name="experiences[<?= $i ?>][ended_at]" value="<?= e($experience['ended_at'] ?? '') ?>"></label>
                <label class="span-2">Description<textarea name="experiences[<?= $i ?>][description]"><?= e($experience['description'] ?? '') ?></textarea></label>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <button class="btn btn-primary">Enregistrer le profil</button>
</form>

import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import mongoose from 'mongoose';
import fileUpload from 'express-fileupload';

dotenv.config();

const app = express();
app.use(cors());
app.use(express.json({ limit: '2mb' }));
app.use(fileUpload());

const MONGO_URI = process.env.MONGO_URI || 'mongodb://localhost:27017/portfolio_system';
await mongoose.connect(MONGO_URI);

const userSchema = new mongoose.Schema({
  id: { type: Number, index: true, unique: true },
  full_name: String,
  email: { type: String, index: true, unique: true, sparse: true },
  password: String,
  profession: String,
  phone: String,
  location: String,
  website: String,
  profile_photo: String,
  summary: String,
  skills: mongoose.Schema.Types.Mixed,
  experience: [mongoose.Schema.Types.Mixed],
  education: [mongoose.Schema.Types.Mixed],
  projects: [mongoose.Schema.Types.Mixed],
}, { timestamps: true });

const User = mongoose.model('User', userSchema);

// Register
app.post('/register', async (req, res) => {
  try {
    const { email } = req.body;
    if (email && await User.findOne({ email })) {
      return res.json({ success: false, message: 'Email already exists' });
    }
    const id = req.body.id ?? Math.floor(Date.now() / 1000);
    await User.updateOne({ id }, { $set: { ...req.body, id } }, { upsert: true });
    res.json({ success: true, user_id: id, message: 'User registered successfully' });
  } catch (e) {
    res.json({ success: false, message: e.message });
  }
});

// Save complete profile
app.post('/save-profile', async (req, res) => {
  try {
    const id = parseInt(req.body.user_id ?? req.body.id ?? 1);
    await User.updateOne({ id }, { $set: { ...req.body, id } }, { upsert: true });
    res.json({ success: true, message: 'Profile saved successfully' });
  } catch (e) {
    res.json({ success: false, message: e.message });
  }
});

// Get profile
app.get('/profile', async (req, res) => {
  try {
    const id = parseInt(req.query.id);
    const profile = await User.findOne({ id }).lean();
    if (!profile) return res.json({ success: false, message: 'User not found' });
    res.json({ success: true, profile });
  } catch (e) {
    res.json({ success: false, message: e.message });
  }
});

// Generate portfolio (basic developer template)
app.post('/generate-portfolio', async (req, res) => {
  try {
    const id = parseInt(req.body.user_id);
    const profile = await User.findOne({ id }).lean();
    if (!profile) return res.json({ success: false, message: 'User not found' });
    const html = buildDeveloperHTML(profile);
    res.json({ success: true, html, profile });
  } catch (e) {
    res.json({ success: false, message: e.message });
  }
});

// Upload (stores file in memory and echoes back a fake URL for demo)
app.post('/upload.php', async (req, res) => {
  try {
    if (!req.files || !req.files.file) return res.json({ success: false, message: 'No file uploaded' });
    const file = req.files.file;
    // In real app, store to disk or cloud and return public URL
    const url = `data:${file.mimetype};base64,${file.data.toString('base64')}`;
    res.json({ success: true, file_url: url });
  } catch (e) {
    res.json({ success: false, message: e.message });
  }
});

function renderTags(items) {
  if (!items || items.length === 0) return '<p>No items listed</p>';
  return items.map(s => `<span class="skill-tag">${escapeHtml(s.name || s.skill_name || s)}</span>`).join('');
}

function renderExperience(items) {
  if (!items || items.length === 0) return '<p>No experience listed</p>';
  return items.map(exp => `
    <div class="experience-item">
      <h3>${escapeHtml(exp.title || exp.job_title || '')}</h3>
      <h4>${escapeHtml(exp.company || '')}</h4>
      <p><strong>Duration:</strong> ${exp.start_date || ''} - ${exp.end_date || 'Present'}</p>
      <p>${escapeHtml(exp.description || '')}</p>
    </div>
  `).join('');
}

function renderEducation(items) {
  if (!items || items.length === 0) return '<p>No education listed</p>';
  return items.map(edu => `
    <div class="education-item">
      <h3>${escapeHtml(edu.degree || '')}</h3>
      <h4>${escapeHtml(edu.institution || '')}</h4>
      <p><strong>Duration:</strong> ${edu.start_date || ''} - ${edu.end_date || ''}</p>
      <p><strong>Grade:</strong> ${escapeHtml(edu.grade || '')}</p>
      <p><strong>Location:</strong> ${escapeHtml(edu.location || '')}</p>
    </div>
  `).join('');
}

function renderProjects(items) {
  if (!items || items.length === 0) return '<p>No projects listed</p>';
  return items.map(p => `
    <div class="project-card">
      <h3>${escapeHtml(p.name || p.project_name || '')}</h3>
      <p><strong>Technologies:</strong> ${escapeHtml(p.technologies || '')}</p>
      <p><strong>Duration:</strong> ${escapeHtml(p.duration || '')}</p>
      <p>${escapeHtml(p.description || '')}</p>
      ${p.url || p.project_url ? `<a href="${escapeAttr(p.url || p.project_url)}" target="_blank">View Project</a>` : ''}
    </div>
  `).join('');
}

function buildDeveloperHTML(profile) {
  const skills = profile.skills || { technical: [], tools: [], soft: [] };
  return `<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>${escapeHtml(profile.full_name || 'Portfolio')}</title><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"><style>*{box-sizing:border-box}body{font-family:Segoe UI,Tahoma,Arial,sans-serif;color:#333} .hero{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:60px 0;text-align:center} .container{max-width:1100px;margin:0 auto;padding:0 20px} .section{padding:50px 0} .skill-tag{display:inline-block;background:#667eea;color:#fff;padding:6px 12px;margin:4px;border-radius:16px;font-size:.9rem} .experience-item{border-left:3px solid #667eea;padding-left:16px;margin-bottom:20px} .project-card{background:#f8f9fa;padding:16px;border-radius:10px;margin-bottom:16px;box-shadow:0 2px 10px rgba(0,0,0,0.08)}</style></head><body><section class="hero"><div class="container">${profile.profile_photo ? `<img src="${escapeAttr(profile.profile_photo)}" alt="Profile" style="width:160px;height:160px;border-radius:50%;object-fit:cover;margin-bottom:1rem;box-shadow:0 10px 30px rgba(0,0,0,0.3);">` : ''}<h1>${escapeHtml(profile.full_name || '')}</h1><p>${escapeHtml(profile.summary || '')}</p><div class="contact-info"><p><i class="fas fa-envelope"></i> ${escapeHtml(profile.email || '')}</p><p><i class="fas fa-phone"></i> ${escapeHtml(profile.phone || '')}</p><p><i class="fas fa-map-marker-alt"></i> ${escapeHtml(profile.location || '')}</p></div></div></section><section class="section"><div class="container"><h2>Skills & Technologies</h2><div><h3>Technical</h3>${renderTags(skills.technical)}<h3>Tools</h3>${renderTags(skills.tools)}<h3>Soft</h3>${renderTags(skills.soft)}</div></div></section><section class="section" style="background:#f8f9fa"><div class="container"><h2>Professional Experience</h2>${renderExperience(profile.experience)}</div></section><section class="section"><div class="container"><h2>Projects</h2>${renderProjects(profile.projects)}</div></section><section class="section" style="background:#f8f9fa"><div class="container"><h2>Education</h2>${renderEducation(profile.education)}</div></section></body></html>`;
}

function escapeHtml(str) {
  return String(str || '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m]));
}
function escapeAttr(str) { return escapeHtml(str).replace(/"/g, '&quot;'); }

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`Node API running on http://localhost:${PORT}`));



import { useState, type ReactNode } from 'react'
import { AnimatePresence, motion } from 'motion/react'
import { ArrowUpRight, ChevronDown, CircleUserRound, Sparkle, Menu, MoveDown, X } from 'lucide-react'

const competitions = [
  ['Compete', '01', 'MCC · BCC · BPC', 'Take on a competition designed to test analysis, strategy, or business thinking.'],
  ['Learn', '02', 'Catalyst Talkshow', 'Hear perspectives from speakers working across sustainability, innovation, business, and related fields.'],
  ['Explore', '03', 'Catalyst Exhibition', 'Discover projects, initiatives, and ideas presented throughout the Summit.'],
]

const benefits = [
  ['01', 'Real-World Challenge', 'Work through problems grounded in practical contexts.'],
  ['02', 'Expert Perspective', 'Exposure to judges, mentors, speakers, and practitioners.'],
  ['03', 'Team Experience', 'Build, decide, and present alongside your team.'],
  ['04', 'Recognition', 'Results and recognition follow each official competition rule.'],
  ['05', 'Network', 'Meet participants and collaborators from different backgrounds.'],
  ['06', 'Growth', 'Sharpen how you frame, analyze, and communicate ideas.'],
]

const faqItems = ['What is catalyst 2026?', 'How can customer intelligence benefit my business?', 'What is conversational intelligence?', 'What is service automation?', 'What is Level AI?']
const timeline = [['20 Sep', 'Registration Opened'], ['20 Sep', 'Registration Closed'], ['14 Nov', 'BCC & BPC Stage 2'], ['22 Nov', 'Catalyst Summit']]
const guidebooks = [['MCC Guidebook', 'Mini Case Competition', 'Open Guidebook'], ['BCC Guidebook', 'Business Case Competition', 'Coming soon'], ['BPC Guidebook', 'Business Plan Competition', 'Coming soon'], ['Summit Visitor Guide', 'Talkshow, Exhibition, and Check-In', 'Coming soon']]

function Eyebrow({ children, light = false }: { children: string; light?: boolean }) {
  return <div className={`eyebrow ${light ? 'text-white' : ''}`}><Sparkle size={20} strokeWidth={1.5} /> <span>{children}</span></div>
}

function Button({ children, secondary = false }: { children: string; secondary?: boolean }) {
  return <button className={`button ${secondary ? 'button-secondary' : ''}`}>{children}<ArrowUpRight size={17} /></button>
}

function Brand({ dark = false }: { dark?: boolean }) {
  return <div className={`brand ${dark ? 'dark' : ''}`}><span className="brand-logo-frame"><AssetImage className="brand-logo" src="/assets/catalyst-logo.png" alt="Catalyst Summit logo" /></span><span>Catalyst<br />Summit</span></div>
}

function AssetImage({ src, alt, className = '' }: { src: string; alt: string; className?: string }) {
  return <img className={className} src={src} alt={alt} onError={(event) => { event.currentTarget.onerror = null; event.currentTarget.src = '/assets/asset-fallback.svg' }} />
}

function Reveal({ children, className = '' }: { children: ReactNode; className?: string }) {
  return <motion.div className={className} initial={{ opacity: 0, y: 28 }} whileInView={{ opacity: 1, y: 0 }} viewport={{ once: true, amount: 0.16 }} transition={{ duration: 0.7, ease: 'easeOut' }}>{children}</motion.div>
}

function App() {
  const [menuOpen, setMenuOpen] = useState(false)
  const [openFaq, setOpenFaq] = useState<number | null>(null)

  return (
    <main>
      <section className="hero-section">
        <motion.div className="hero-image" initial={{ scale: 1.1 }} animate={{ scale: 1 }} transition={{ duration: 2, ease: [0.22, 1, 0.36, 1] }} />
        <header className="nav-shell">
          <Brand />
          <nav className={menuOpen ? 'nav-links open' : 'nav-links'}><a href="#about">Pre-Event 1</a><a href="#experience">Pre-Event 2</a><a href="#summit">Main Event</a></nav>
          <div className="nav-actions"><button className="guide">Guidebook</button><button className="register">Register now</button></div>
          <button className="menu-button" onClick={() => setMenuOpen(!menuOpen)} aria-label="Toggle menu">{menuOpen ? <X /> : <Menu />}</button>
        </header>
        <div className="hero-copy">
          <motion.p initial={{ x: -40, opacity: 0 }} animate={{ x: 0, opacity: 1 }} transition={{ duration: 0.8, delay: 0.15, ease: 'easeOut' }}>Main Event</motion.p>
          <div><motion.h1 initial={{ y: 40, opacity: 0 }} animate={{ y: 0, opacity: 1 }} transition={{ duration: 0.9, delay: 0.3, ease: 'easeOut' }}>Unite for a transformative experience<br />the heart of Catalyst Summit awaits.</motion.h1><motion.p className="hero-sub" initial={{ y: 40, opacity: 0 }} animate={{ y: 0, opacity: 0.82 }} transition={{ duration: 0.9, delay: 0.45, ease: 'easeOut' }}>Join us for a life-changing experience the essence of the Catalyst Summit awaits you.</motion.p></div>
          <div className="scroll-label"><MoveDown size={20} /> Scroll to explore</div>
        </div>
      </section>

      <section className="section snapshot" id="summit"><Reveal><div className="section-heading"><Eyebrow> Main event snapshot</Eyebrow><p>One Summit Pass gives access to both the Talkshow and Exhibition, taking place on 22 November 2026.</p></div><div className="stat-grid">{[['03', 'Competitions'], ['01', 'Talkshow'], ['01', 'Exhibition'], ['01', 'Summit Pass']].map(([n, label]) => <div className="stat" key={label}><strong>{n}</strong><span>{label}</span></div>)}</div></Reveal></section>

      <section className="section about" id="about"><Reveal><Eyebrow>About Catalyst Summit</Eyebrow><div className="split-copy"><h2>Where the Catalyst <em>journey comes together.</em></h2><p>Catalyst Summit brings the different parts of Catalyst 2026 into one shared experience. Participants can compete through MCC, BCC, or BPC, while visitors can join the Talkshow and explore the Exhibition throughout the day.</p></div></Reveal></section>

      <section className="section pale" id="experience"><Reveal><Eyebrow>Summit experience</Eyebrow><h2>Three ways to experience Catalyst Summit.</h2><div className="card-grid">{competitions.map(([label, number, title, text]) => <motion.article className="info-card" whileHover={{ y: -8 }} key={number}><div className="card-top"><span>{label}</span><b>{number}</b></div><h3>{title}</h3><p>{text}</p><a href="#competitions">Explore <ArrowUpRight size={16} /></a></motion.article>)}</div></Reveal></section>

      <section className="section" id="competitions"><Reveal><Eyebrow>Compete at Catalyst</Eyebrow><h2>Three competitions.<br />Different ways to solve.</h2><p className="lead">Explore the competition that matches<br />your background, team, and approach.</p><div className="card-grid competition-grid">{['Mini Case Competition', 'Business Case Competition', 'Business Plan Competition'].map((title, index) => <motion.article className="competition-card" whileHover={{ scale: 1.02 }} key={title}><span className="tag">{index === 0 ? 'Registration Open Soon' : 'Upcoming'}</span><h3>{title}</h3><p>{['A compact case challenge built around clear thinking and focused decision-making.', 'Analyze a business problem and turn your diagnosis into a structured recommendation.', 'Develop a business idea into a plan that connects opportunity, feasibility, and impact.'][index]}</p><a href="#summit">Explore {['MCC', 'BCC', 'BPC'][index]} <ArrowUpRight size={16} /></a></motion.article>)}</div></Reveal></section>

      <section className="section golden-ticket"><Reveal><Eyebrow>Golden Ticket</Eyebrow><h2>A route from MCC into BCC.</h2><p className="lead">Selected MCC participants may receive a Golden Ticket benefit toward the Business Case Competition. Participants still complete the standard BCC registration flow.</p><Button>Start with MCC</Button></Reveal></section>

      <section className="section journey"><Reveal><Eyebrow>Competition journey</Eyebrow><h2>From sign-up to the Summit stage.</h2><div className="journey-grid">{[['01', 'Choose Competition', 'Find the competition that fits your team.'], ['02', 'Register Your Team', 'Complete team details and registration requirements.'], ['03', 'Verification', 'Catalyst reviews your registration and payment.'], ['04', 'Submission', 'Upload your work when the relevant stage opens.'], ['05', 'Qualification', 'Selected teams continue to the next stage.'], ['06', 'Catalyst Summit', 'Qualified teams complete their final competition journey.']].map(([n, title, text]) => <div className="journey-item" key={n}><b>{n}</b><h3>{title}</h3><p>{text}</p></div>)}</div></Reveal></section>

      <section className="section timeline"><Reveal><Eyebrow>Main event timeline</Eyebrow><h2>Key dates on the road to 22 November.</h2><div className="timeline-grid">{timeline.map(([date, label], index) => <div className={`timeline-item ${index < 2 ? 'complete' : ''}`} key={label}><span className="timeline-dot" /><strong>{date}</strong><p>{label}</p></div>)}</div></Reveal></section>

      <section className="section talkshow"><Reveal><div><Eyebrow>Talkshow</Eyebrow><h2>A conversation worth making time for.</h2><p className="lead">The Catalyst Talkshow brings speakers and participants into one conversation around the themes and questions connected to HORIZON.</p><Button>Get Summit Pass</Button></div></Reveal><Reveal><div className="feature-card"><span>Featured Session</span><h3>HORIZON in Conversation</h3><div className="definition"><p>Speaker<strong>To Be Announced</strong></p><p>Date &amp; Time<strong>22 Nov 2026</strong></p><p>Role<strong>To Be Announced</strong></p><p>Stage<strong>To Be Confirmed</strong></p></div></div></Reveal></section>

      <section className="section exhibition"><Reveal><Eyebrow>Exhibition</Eyebrow><h2>Explore what's being presented beyond the stage.</h2><p className="lead">The Exhibition gives visitors space to browse selected projects, initiatives, and displays throughout Catalyst Summit.</p><div className="image-row">{['exhibit-1.jpeg', 'exhibit-2.png', 'exhibit-3.png'].map((image) => <figure key={image}><AssetImage src={`/assets/${image}`} alt="Catalyst exhibition" /><figcaption>Exhibitor<br /><strong>To Be Announced</strong></figcaption></figure>)}</div><Button>Get Summit Pass</Button></Reveal></section>

      <section className="section pass" id="pass"><Reveal><div><Eyebrow>Summit Pass</Eyebrow><h2>One Summit Pass for<br />the Talkshow and Exhibition.</h2><p className="lead">Your Summit Pass gives you access to both public experiences at Catalyst Summit on 22 November 2026.</p><p className="note">Competition registration does <b>not</b> include a Summit Pass.</p></div></Reveal><Reveal><div className="pass-card"><h3>Summit Pass <span>Coming Soon</span></h3>{['Talkshow Access', 'Exhibition Access', 'QR Check-In', 'Attendance Certificate'].map(item => <p key={item}>✓ {item}</p>)}<Button>Get Summit Pass</Button><small>Pricing and availability will be announced ahead of the Summit.</small></div></Reveal></section>

      <section className="section benefits"><Reveal><Eyebrow>Why join Catalyst</Eyebrow><h2>More than a final result.</h2><p className="lead">The value of Catalyst goes beyond ranking. Participants gain experience, feedback, connections, and recognition throughout the journey.</p><div className="benefit-list">{benefits.map(([n, title, text]) => <div key={n}><b>{n}</b><h3>{title}</h3><p>{text}</p></div>)}</div></Reveal></section>

      <section className="section people"><Reveal><Eyebrow>People of Catalyst Summit</Eyebrow><h2>Meet the people <em>behind the conversations and competitions.</em></h2><p className="lead">Speakers, judges, and mentors.</p><div className="people-grid">{['person-1.jpeg', 'person-2.png', 'person-3.png'].map((image, i) => <figure key={image}><AssetImage src={`/assets/${image}`} alt="Catalyst participant" /><figcaption>{['Talkshow Speaker', 'Competition Judge', 'Mentor'][i]}<strong>To Be Announced</strong></figcaption></figure>)}<div className="announcement"><CircleUserRound size={28} />Speaker Announcement<br />Coming Soon</div></div></Reveal></section>

      <section className="section guidebooks"><Reveal><Eyebrow>Guidebooks &amp; resources</Eyebrow><h2>Start with the official guidebook.</h2><p className="lead">Competition rules, eligibility, timelines, and submission requirements are available in each guidebook.</p><div className="resource-grid">{guidebooks.map(([title, subtitle, action]) => <article key={title}><CircleUserRound size={24} /><div><h3>{title}</h3><p>{subtitle}</p></div><span>{action}</span></article>)}</div></Reveal></section>

      <section className="section partners"><Reveal><Eyebrow>Sponsors &amp; partners</Eyebrow><h2>Supported by our strategic partners.</h2>{['Strategic Partners', 'Sponsors', 'Media Partners'].map((group) => <div className="partner-row" key={group}><b>{group}</b><div><span>CATALYST</span><span>UNAIR</span><span>HORIZON</span></div></div>)}</Reveal></section>

      <section className="section faq"><Reveal><Eyebrow>FAQ</Eyebrow><div className="faq-grid"><div><h2>Question<br />before you join?</h2><p className="lead">Any more questions?</p><a href="mailto:hello@catalystsummit.id">Contact us <ArrowUpRight size={18} /></a></div><div>{faqItems.map((item, i) => <div className="faq-item" key={item}><button onClick={() => setOpenFaq(openFaq === i ? null : i)}>{item}<ChevronDown className={openFaq === i ? 'rotate' : ''} size={18} /></button><AnimatePresence>{openFaq === i && <motion.p initial={{ height: 0, opacity: 0 }} animate={{ height: 'auto', opacity: 1 }} exit={{ height: 0, opacity: 0 }}>Details about Catalyst Summit will be announced in the official guidebook.</motion.p>}</AnimatePresence></div>)}</div></div></Reveal></section>

      <section className="cta">
        <div className="cta-bg-container">
          <motion.div 
            className="cta-bg" 
            initial={{ width: '40px', height: '10px' }} 
            whileInView={{ width: '100%', height: '100%' }} 
            viewport={{ once: true, amount: 0.5 }}
            transition={{ duration: 1.2, ease: [0.16, 1, 0.3, 1] }} 
          />
        </div>
        <motion.div 
          className="cta-content"
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true, amount: 0.5 }}
          transition={{ delay: 0.7, duration: 0.8, ease: 'easeOut' }}
        >
          <Eyebrow light>Be part of the summit</Eyebrow>
          <h2>Choose how you'll be part<br />of the summit.</h2>
          <p>Join a competition or experience the talkshow and exhibition with a summit pass.</p>
          <Button>Explore Pre-Event 2</Button>
        </motion.div>
      </section>
      <footer><div><Brand dark /><p>Experience mental without limits personalized insights and an Ape friend that evolves with you.</p><b>Made for people. Built for happy.</b></div><div className="footer-links"><div><b>Explore</b><span>Home</span><span>Pre-Event 1</span><span>Pre-Event 2</span><span>Catalyst Summit</span></div><div><b>Competitions</b><span>MCC</span><span>BCC</span><span>BPC</span></div><div><b>Resources</b><span>Guidebooks</span><span>FAQ</span><span>Timeline</span></div><div><b>Connect</b><span>Instagram</span><span>Contact</span><span>SRE UNAIR</span></div></div></footer>
    </main>
  )
}

export default App

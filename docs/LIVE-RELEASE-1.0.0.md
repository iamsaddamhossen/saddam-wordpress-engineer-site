# Saddam Hossen WordPress Engineering Site — Live Release 1.0.0

## What changed in the final live package

### Shared inner-page hero system
Every inner page now uses the same light blue gradient hero language as Recommended Tools. The homepage remains the only dark hero because it is the primary brand statement.

Covered templates include:
- Engineering
- Writing archive
- Contact
- Experience
- Projects
- Case studies
- Recommended Tools
- Resources
- Normal pages
- Single projects and case studies
- Single technical articles
- Service archive and service pages
- 404

### Single article reading experience
- Light gradient article hero
- Wider title and excerpt area
- Premium white reading card
- Sticky table of contents on desktop
- Existing heading numbers are removed from TOC labels to prevent double numbering
- Premium author profile with Saddam Hossen's photo
- Related reading cards
- Improved code, table, list, quote and callout spacing
- Responsive one-column layout on small screens

### Engineering page closing section
The final quality and repository section is now a light premium section. It no longer creates an awkward black block immediately above the footer.

### Footer
- CTA width now matches the normal site container
- Reduced vertical size
- Removed decorative line before eyebrow labels
- Buttons remain compact and readable

### SEO and AI search foundation
- Canonical URLs
- Meta descriptions
- Open Graph and Twitter metadata
- Context-aware structured data
- BlogPosting author and date information
- ProfilePage and Person data
- BreadcrumbList data
- Sitemap discovery
- Robots controls
- Visible author identity
- Internal related reading
- Helpful technical content structure

No SEO implementation can guarantee a ranking, citation or AI answer. The site focuses on eligibility, clarity, originality and useful content.

## Installation order

1. Back up the database and `wp-content`.
2. Upload `saddam-engineer-core-v1.0.0.zip` from Plugins > Add New > Upload Plugin.
3. Replace the existing plugin and activate it.
4. Upload `saddam-engineer-portfolio-v1.0.0.zip` from Appearance > Themes > Add New > Upload Theme.
5. Replace the existing theme and activate it.
6. Run Saddam Engineer > Starter Content > Import / Repair Site Content once.
7. Open Settings > Permalinks and click Save Changes.
8. Clear WordPress, hosting, CDN and browser caches.
9. Confirm Theme 1.0.0 and Plugin 1.0.0.

## Mandatory live checks

### Desktop and mobile
- Homepage
- Engineering
- Writing
- One long article
- Recommended Tools
- Projects
- Case Studies
- Experience
- Contact
- Privacy and Terms

### Article checks
- TOC does not show duplicate numbers
- Author photo loads
- Related articles display correctly
- Tables do not overflow
- Code blocks scroll on small screens
- Back-to-top stays on the left

### SEO checks
- View source for canonical and metadata
- Test one article in Google's Rich Results Test
- Test the homepage Person/ProfilePage data in Schema Validator
- Submit the WordPress sitemap in Search Console
- Inspect homepage, Engineering and one article in URL Inspection

## Content publishing standard
Each new article should answer a real question from production work, support, learning or career development. Include:
- The specific problem
- Context and constraints
- The decision or workflow
- A concrete example
- Risks and limitations
- A practical checklist
- A related project, source file or official reference when relevant

Avoid publishing many shallow articles at once. One deeply useful article is more valuable than several generic summaries.

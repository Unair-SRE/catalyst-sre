# Main Event Design QA

- Source visual truth: `C:\Users\sutha\Downloads\Main Event Ryan.png`
- Source dimensions: 1440 × 12651 px.
- Implementation: `http://127.0.0.1:8000/main-event` (browser captures in the Codex in-app browser; captures are session-backed and not written into the repository).
- Desktop evidence: 1440 × 900 CSS viewport for focused hero, competition-card, Golden Ticket, journey, Talkshow, Summit Pass, guidebook, and benefit-row checks. A 1280 × 720 full-page pass was retained for macro composition review.
- Mobile evidence: 390 × 844 and 360 × 800 CSS viewports; rendered document widths were 375 px and 345 px because of browser chrome/scrollbars.
- State: public Main Event, default content, 25 September 2026 WIB-derived competition/timeline state.

## Findings

No actionable P0/P1/P2 mismatch remains after the revisions below.

- Typography uses the repository PP Mori assets and follows the Homepage hierarchy. The hero, section headings, numeric metrics, metadata, and body copy retain the intended editorial contrast.
- Spacing and layout rhythm follow the Figma composition: asymmetric hero, centered statistics, open About composition, separated experience cards, editorial split sections, full-width journey/timeline, and a compact closing CTA.
- Catalyst colors and section surfaces match the brief: `#E0EDED` Summit Experience, restrained white/green/yellow surfaces, animated green/yellow accents, and muted gray supporting copy.
- The supplied solar-panel asset is sharp and correctly cropped. Missing speaker, exhibitor, and partner imagery remains explicitly marked TBA rather than being replaced with invented assets.
- Copy and ordering match the revision brief, including 29 Nov 2026, the separate Summit Pass rule, the Golden Ticket boundary, and Main Event timeline dates.

## Comparison History

1. Initial implementation review found a P2 animation-clipping issue: rotating border pseudo-elements could extend outside Golden Ticket/Talkshow cards. The reusable border class now clips its animated layer and uses an oversized internal gradient layer. Focused post-fix browser captures show no escaping polygon or page overflow.
2. The latest brief supersedes the earlier six-column journey: the final desktop layout is now an intentional 3-column × 2-row editorial grid, with a 2-column tablet and single-column mobile adaptation.
3. The hero now uses the supplied arrow SVG, a compact Horizon label, and no oversized HORIZON word. At-a-glance metric clusters are centered.
4. Competition cards now reuse the Homepage card hierarchy, while Talkshow, Summit Pass, Guidebook, Why Join, and FAQ follow the supplied final compositions and copy treatment.
5. Mobile reviews at 390 px and 360 px confirmed no horizontal overflow, readable wrapping, stacked CTA behavior, and an intact shared mobile navigation.

## Interaction Evidence

- Exhibition marquee computed transform changed automatically over 350 ms and reports `animation-iteration-count: infinite`.
- Homepage reveal integration reports `data-reveal-ready="true"` and reveals sections once as they enter the viewport.
- People filter changed from All to Speaker and reduced visible cards from eight to three while updating its live region.
- Golden Ticket uses three 2000 ms CSS motion tracks. Runtime samples changed from opacity `0.614852` to `0.972419` and scale matrix `0.985346` to `1`, while reporting `animation-name: main-event-golden-scale` and infinite iteration.
- Animated competition/Golden Ticket borders report `animation-name: home-active-border`.
- Browser console: no errors or warnings during the tested Main Event states.

## Focused Region Comparison

- Hero: matched against the source for height, two-column hierarchy, bottom divider, left down control, right scroll cue, and image contrast.
- Talkshow: matched for the editorial left/right split, 288 px transparent-to-yellow surface, compact white/yellow information card, and CTA placement beneath the description.
- Summit Pass: verified as a white split composition with a separate-registration notice and responsive compact pass card.
- Why Join: verified at 1440 px and 360 px with supplied per-benefit SVGs and responsive number/icon/title/description hierarchy.
- Mobile hero: checked at 390 × 844 for typography wrapping, navigation contrast, readable body copy, and overflow.
- Full-page captures were used for macro section order and rhythm. Because animated/reveal sections can create stitching ghosts in browser full-page capture, focused static viewport captures were used for typography, clipping, and alignment judgments.

## Follow-up Polish

- P3: Replace TBA speaker, exhibitor, and partner visuals only when official assets are supplied.
- P3: Connect Guidebook, Summit Pass, and Exhibition placeholder actions when final destinations are confirmed.

## Final Result

final result: passed

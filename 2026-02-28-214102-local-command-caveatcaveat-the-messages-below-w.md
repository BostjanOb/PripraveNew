Project Review Report

High Priority

4. Badge threshold duplication

DocumentController::getAuthorBadgeId() duplicates thresholds already in
BadgeService.

---
Medium Priority

7. N+1 query risk in BadgeService/User count methods

uploadCount(), downloadCount(), etc. each fire separate queries. Called
together in BadgeService::getEarnedBadgeIds() = 7+ queries per user.

10. CreateDocument::submit() missing error handling

tempnam(), ZipArchive::open() return values unchecked. Large method (~50
lines) should be extracted.

11. Redundant $navigationLabel in Filament resources

All resources set $navigationLabel identical to $pluralModelLabel (Filament
derives it automatically).

12. FortifyServiceProvider no-op registration

RedirectIfTwoFactorAuthenticatable is Fortify's own default -- registering it
is a no-op.

13. Inline SVGs instead of blade-icons

uploaded-documents-tab, latest-documents, create-document use raw SVGs while
browse-documents uses <x-icon-*> components.

14. Alpine/Livewire state duplication

create-document.blade.php duplicates Livewire state in Alpine x-data instead
of using $wire.entangle().

15. name/display_name sync issue

CreateNewUser and SocialAuthController both set name = display_name, but
UpdateUserProfileInformation doesn't sync them on update.

---
Low Priority

- Empty ->filters([]) and getRelations() scaffolding in 9 Filament resources
- Duplicate icon (OutlinedRectangleStack) on FaqResource and CategoryResource
- User::isPioneer() uses whereRaw where whereColumn would work
- Redundant array_values(array_keys(...)) in
BrowseMeilisearchQueryBuilder.php:19
- BrowseDocuments::removeFilter() double-calls resetPage()
- BadgeRegistry has repeated color definitions; find() uses linear search
- Unnecessary ->getStateUsing() on email_verified_at IconColumns (truthy/falsy
  suffices)
- LatestDocuments re-queries SchoolType on every render (should cache in
mount())
- 3 tab Blade templates have nearly identical empty-state/list markup

---
Want me to start fixing any of these? I'd suggest tackling the high-priority
items first (::query() removal, wire:key additions, policy extraction).

✻ Brewed for 3m 24s

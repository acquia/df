# TODO

- Fix Drupal core, so that a core patch is no longer necessary.
- Test coverage
- The contextual links' `?destination=` query argument does not get updated.


# Disabling RefreshLess on specific links

RefreshLess can be disabled on a per-link basis by setting the `data-refreshless-exclude` attribute on it:
```
<a href="/somewhere" data-refreshless-exclude">Ignored by RefreshLess</a>
```

Links with RefreshLess enabled will be handled normally by the browser.


# Events
- `refreshless:load` dispatched on `window` whenever a new page has loaded through RefreshLess


# API

- `Drupal.RefreshLess.visit(url)`


# How RefreshLess uses the History API

- Every URL transition within Drupal is tracked: both inter-page navigation and intra-page (fragment) navigation. See the `State` object.
- Every `State` object has a position.
- The current position in the history stack is tracked. This allows us to detect whether the user is navigating backward or forward.
- Scroll restoration is handled entirely by History API, hence we don't need to track the scroll position at all.


# Architecture

- Classes: Url (n), State (n), HistoryNavigation (1), LinkNavigation (1), Controller (1)
- State uses Url
- HistoryNavigation uses State
- LinkNavigation only uses the public API
- Controller uses HistoryBasedNavigation, LinkNavigation and State


# Requirements

- Theme always has the same layout (e.g. no conditional `<body>` classes based on current path/route).
- Theme always has the same set of regions.

Or it is at least configured to always the same layout and regions.

Example: Bartik can be problematic, but it usually is not, because it seldomly is configured to use the flexibility it provides. As long as you just always use the first sidebar and there always is some block visible in there, there is no problem. See `bartik_preprocess_html().

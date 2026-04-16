@extends($layout ?? 'layouts.render')

@section('content')
@php
	$name = data_get($cv, 'personal_name') ?: (data_get($cv, 'user.name') ?? 'CV');
	$email = data_get($cv, 'personal_email') ?: (data_get($cv, 'user.email') ?? '');
	$title = data_get($cv, 'title');
	$summary = data_get($cv, 'summary');
	$phone = data_get($cv, 'personal_phone');
	$location = data_get($cv, 'personal_location');
	$linkedin = data_get($cv, 'personal_linkedin');
	$website = data_get($cv, 'personal_website');
	$thumbMode = ($layout === 'layouts.thumb');

	$photoPreview = data_get($cv, 'photo_preview_url');
	$photoPath = data_get($cv, 'photo_path');
	$photo = $photoPreview ?: ($photoPath ? asset('storage/'.$photoPath) : null);

	$accent = strtoupper((string) data_get($cv, 'accent_color', '#7C3AED'));
	$themes = [
		'#7C3AED' => ['accent' => '#7C3AED', 'deep' => '#4C1D95', 'soft' => '#EDE9FE'],
		'#0EA5A4' => ['accent' => '#0EA5A4', 'deep' => '#0F766E', 'soft' => '#CCFBF1'],
		'#3B82F6' => ['accent' => '#3B82F6', 'deep' => '#1D4ED8', 'soft' => '#DBEAFE'],
		'#EA580C' => ['accent' => '#EA580C', 'deep' => '#C2410C', 'soft' => '#FFEDD5'],
		'#334155' => ['accent' => '#334155', 'deep' => '#0F172A', 'soft' => '#E2E8F0'],
		'#166534' => ['accent' => '#166534', 'deep' => '#14532D', 'soft' => '#DCFCE7'],
		'#BE123C' => ['accent' => '#BE123C', 'deep' => '#9F1239', 'soft' => '#FFE4E6'],
	];
	$tone = $themes[$accent] ?? $themes['#7C3AED'];

	$experiences = collect(data_get($cv, 'experiences', []));
	$educations = collect(data_get($cv, 'educations', []));
	$skills = collect(data_get($cv, 'skills', []));

	$placeholderFlags = collect(data_get($cv, 'preview_placeholder_flags', []));
	$isPlaceholder = fn (string $key): bool => (bool) $placeholderFlags->get($key, false);
	$itemPlaceholder = fn ($item, string $field): bool => (bool) data_get($item, '_placeholder.'.$field, false);
@endphp

<style>
@import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap');

.cv-paper {
	--ac: {{ $tone['accent'] }};
	--ac-deep: {{ $tone['deep'] }};
	--ac-soft: {{ $tone['soft'] }};
}

.cv-sidebar {
	font-family: 'Nunito Sans', sans-serif;
	color: #0f172a;
	font-size: 12.7px;
	line-height: 1.62;
	display: grid;
	grid-template-columns: 35% 1fr;
	min-height: 100%;
}

.cv-sidebar .cv-placeholder { opacity: .56; font-style: italic; }

.cv-side {
	background: linear-gradient(170deg, var(--ac-deep), var(--ac));
	color: #f8fafc;
	padding: 18px 14px;
}

.cv-main {
	background: #ffffff;
	padding: 18px 16px;
}

.cv-side-photo {
	width: 88px;
	height: 88px;
	border-radius: 14px;
	object-fit: cover;
	border: 2px solid #ffffff8f;
	margin-bottom: 12px;
}

.cv-side-name {
	margin: 0;
	font-family: 'Playfair Display', serif;
	font-size: 28px;
	line-height: 1.06;
}

.cv-side-role {
	margin-top: 6px;
	font-size: 10.5px;
	letter-spacing: .18em;
	text-transform: uppercase;
	font-weight: 700;
	color: #dbeafe;
}

.cv-side-title {
	margin: 14px 0 8px;
	font-size: 10px;
	letter-spacing: .2em;
	text-transform: uppercase;
	font-weight: 800;
	color: #e0e7ff;
}

.cv-side-line {
	font-size: 11.2px;
	margin-bottom: 7px;
	word-break: break-word;
}

.cv-side-chip {
	display: inline-block;
	margin: 0 6px 6px 0;
	padding: 4px 8px;
	border-radius: 999px;
	border: 1px solid #ffffff5f;
	background: #ffffff1a;
	font-size: 10.5px;
	font-weight: 700;
}

.cv-main-name {
	margin: 0;
	font-family: 'Playfair Display', serif;
	font-size: 28px;
	color: #0f172a;
	display: none;
}

.cv-main-section {
	margin-bottom: 14px;
}

.cv-main-heading {
	margin: 0 0 8px;
	font-size: 10px;
	letter-spacing: .2em;
	text-transform: uppercase;
	color: var(--ac-deep);
	font-weight: 800;
}

.cv-summary {
	margin: 0;
	color: #334155;
	line-height: 1.74;
}

.cv-entry {
	border-left: 2px solid var(--ac-soft);
	padding-left: 9px;
	margin-bottom: 10px;
}

.cv-entry-top {
	display: flex;
	justify-content: space-between;
	gap: 10px;
	align-items: baseline;
}

.cv-entry-role {
	margin: 0;
	font-size: 13px;
	font-weight: 800;
}

.cv-entry-date {
	font-size: 10.2px;
	color: var(--ac-deep);
	font-weight: 700;
	white-space: nowrap;
}

.cv-entry-sub {
	font-size: 11.3px;
	color: #475569;
	margin-top: 2px;
}

.cv-entry-desc {
	margin-top: 5px;
	font-size: 11.7px;
	color: #334155;
	line-height: 1.65;
}

@media (max-width: 760px) {
	.cv-sidebar {
		grid-template-columns: 1fr;
	}

	.cv-main-name { display: block; margin-bottom: 10px; }
}
</style>

<div class="cv-sidebar">
	<aside class="cv-side">
		@if($photo)<img src="{{ $photo }}" alt="Photo" class="cv-side-photo">@endif

		<h1 class="cv-side-name {{ $isPlaceholder('personal_name') ? 'cv-placeholder' : '' }}">{{ $thumbMode ? Str::limit($name, 20) : $name }}</h1>
		@if($title)<div class="cv-side-role {{ $isPlaceholder('title') ? 'cv-placeholder' : '' }}">{{ $title }}</div>@endif

		<h2 class="cv-side-title">Contact</h2>
		@if($email)<div class="cv-side-line {{ $isPlaceholder('personal_email') ? 'cv-placeholder' : '' }}">{{ $email }}</div>@endif
		@if($phone)<div class="cv-side-line">{{ $phone }}</div>@endif
		@if($location)<div class="cv-side-line">{{ $location }}</div>@endif
		@if($linkedin)<div class="cv-side-line">{{ $linkedin }}</div>@endif
		@if($website)<div class="cv-side-line">{{ $website }}</div>@endif

		<h2 class="cv-side-title">Skills</h2>
		@foreach($skills as $skill)
			<span class="cv-side-chip {{ $itemPlaceholder($skill, 'name') ? 'cv-placeholder' : '' }}">{{ data_get($skill, 'name') }}</span>
		@endforeach
	</aside>

	<main class="cv-main">
		<h1 class="cv-main-name {{ $isPlaceholder('personal_name') ? 'cv-placeholder' : '' }}">{{ $name }}</h1>

		@if($summary)
			<section class="cv-main-section">
				<h2 class="cv-main-heading">Profile</h2>
				<p class="cv-summary {{ $isPlaceholder('summary') ? 'cv-placeholder' : '' }}">{{ $thumbMode ? Str::limit($summary, 220) : $summary }}</p>
			</section>
		@endif

		<section class="cv-main-section">
			<h2 class="cv-main-heading">Experience</h2>
			@foreach(($thumbMode ? $experiences->take(3) : $experiences) as $exp)
				<article class="cv-entry">
					<div class="cv-entry-top">
						<p class="cv-entry-role {{ $itemPlaceholder($exp, 'position') ? 'cv-placeholder' : '' }}">{{ data_get($exp, 'position') }}</p>
						<span class="cv-entry-date {{ ($itemPlaceholder($exp, 'start_date') || $itemPlaceholder($exp, 'end_date')) ? 'cv-placeholder' : '' }}">{{ data_get($exp, 'start_date') }}{{ data_get($exp, 'end_date') ? ' - '.data_get($exp, 'end_date') : ' - Present' }}</span>
					</div>
					<div class="cv-entry-sub {{ $itemPlaceholder($exp, 'company') ? 'cv-placeholder' : '' }}">{{ data_get($exp, 'company') }}</div>
					@if(data_get($exp, 'description'))
						<div class="cv-entry-desc {{ $itemPlaceholder($exp, 'description') ? 'cv-placeholder' : '' }}">{{ $thumbMode ? Str::limit((string) data_get($exp, 'description'), 170) : data_get($exp, 'description') }}</div>
					@endif
				</article>
			@endforeach
		</section>

		<section class="cv-main-section">
			<h2 class="cv-main-heading">Education</h2>
			@foreach($educations as $edu)
				<article class="cv-entry">
					<div class="cv-entry-top">
						<p class="cv-entry-role {{ $itemPlaceholder($edu, 'school') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'school') }}</p>
						<span class="cv-entry-date {{ $itemPlaceholder($edu, 'year') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'year') }}</span>
					</div>
					<div class="cv-entry-sub {{ $itemPlaceholder($edu, 'degree') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'degree') }}</div>
				</article>
			@endforeach
		</section>
	</main>
</div>
@endsection

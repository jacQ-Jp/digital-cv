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
@import url('https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&family=Libre+Baskerville:wght@400;700&display=swap');

.cv-paper {
	--ac: {{ $tone['accent'] }};
	--ac-deep: {{ $tone['deep'] }};
	--ac-soft: {{ $tone['soft'] }};
}

.cv-poster {
	font-family: 'Libre Baskerville', serif;
	color: #0f172a;
	font-size: 12.4px;
	line-height: 1.68;
}

.cv-poster .cv-placeholder { opacity: .54; font-style: italic; }

.cv-poster-top {
	border: 2px solid #0f172a;
	position: relative;
	padding: 16px 14px 13px;
}

.cv-poster-tag {
	position: absolute;
	top: -10px;
	left: 12px;
	background: #fff;
	padding: 0 6px;
	font-family: 'Oswald', sans-serif;
	text-transform: uppercase;
	letter-spacing: .16em;
	color: var(--ac-deep);
	font-size: 10px;
}

.cv-poster-name {
	margin: 0;
	font-family: 'Oswald', sans-serif;
	font-size: 40px;
	line-height: .96;
	letter-spacing: .02em;
	text-transform: uppercase;
}

.cv-poster-role {
	margin-top: 8px;
	font-family: 'Oswald', sans-serif;
	font-size: 13px;
	text-transform: uppercase;
	letter-spacing: .12em;
	color: var(--ac-deep);
}

.cv-poster-contact {
	margin-top: 9px;
	font-size: 11.2px;
	color: #334155;
	display: flex;
	flex-wrap: wrap;
	gap: 6px 12px;
}

.cv-poster-grid {
	margin-top: 13px;
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 12px;
}

.cv-card {
	border: 1px solid #dbe3ec;
	padding: 10px 11px;
	background: linear-gradient(180deg, #fff, #f8fafc);
}

.cv-card-title {
	margin: 0 0 8px;
	font-family: 'Oswald', sans-serif;
	font-size: 13px;
	letter-spacing: .1em;
	text-transform: uppercase;
	color: var(--ac-deep);
}

.cv-card-summary {
	margin: 0;
	font-size: 11.8px;
	line-height: 1.78;
	color: #334155;
}

.cv-list-item {
	margin-bottom: 9px;
	padding-bottom: 9px;
	border-bottom: 1px dashed #dbe3ec;
}

.cv-list-item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: 0; }

.cv-list-head {
	display: flex;
	justify-content: space-between;
	gap: 10px;
	align-items: baseline;
}

.cv-list-role {
	margin: 0;
	font-family: 'Oswald', sans-serif;
	font-size: 14px;
	font-weight: 600;
	letter-spacing: .03em;
}

.cv-list-date {
	font-size: 10px;
	color: var(--ac-deep);
	font-family: 'Oswald', sans-serif;
	letter-spacing: .04em;
}

.cv-list-sub {
	font-size: 11px;
	color: #64748b;
	margin-top: 2px;
}

.cv-list-desc {
	margin-top: 5px;
	font-size: 11.4px;
	color: #334155;
}

.cv-skill-row {
	display: flex;
	flex-wrap: wrap;
	gap: 7px;
}

.cv-skill-badge {
	font-family: 'Oswald', sans-serif;
	font-size: 10px;
	letter-spacing: .07em;
	text-transform: uppercase;
	color: #0f172a;
	border: 1px solid var(--ac);
	background: var(--ac-soft);
	padding: 3px 8px;
}

@media (max-width: 760px) {
	.cv-poster-grid { grid-template-columns: 1fr; }
}
</style>

<div class="cv-poster">
	<header class="cv-poster-top">
		<div class="cv-poster-tag">Curriculum Vitae</div>
		<h1 class="cv-poster-name {{ $isPlaceholder('personal_name') ? 'cv-placeholder' : '' }}">{{ $thumbMode ? Str::limit($name, 18) : $name }}</h1>
		@if($title)<div class="cv-poster-role {{ $isPlaceholder('title') ? 'cv-placeholder' : '' }}">{{ $title }}</div>@endif
		<div class="cv-poster-contact">
			@if($email)<span class="{{ $isPlaceholder('personal_email') ? 'cv-placeholder' : '' }}">{{ $email }}</span>@endif
			@if($phone)<span>{{ $phone }}</span>@endif
			@if($location)<span>{{ $location }}</span>@endif
			@if($linkedin)<span>{{ $linkedin }}</span>@endif
			@if($website)<span>{{ $website }}</span>@endif
		</div>
	</header>

	<div class="cv-poster-grid">
		<section class="cv-card" style="grid-column: 1 / -1;">
			<h2 class="cv-card-title">Summary</h2>
			<p class="cv-card-summary {{ $isPlaceholder('summary') ? 'cv-placeholder' : '' }}">{{ $thumbMode ? Str::limit((string) $summary, 230) : $summary }}</p>
		</section>

		<section class="cv-card">
			<h2 class="cv-card-title">Experience</h2>
			@foreach(($thumbMode ? $experiences->take(3) : $experiences) as $exp)
				<article class="cv-list-item">
					<div class="cv-list-head">
						<p class="cv-list-role {{ $itemPlaceholder($exp, 'position') ? 'cv-placeholder' : '' }}">{{ data_get($exp, 'position') }}</p>
						<span class="cv-list-date {{ ($itemPlaceholder($exp, 'start_date') || $itemPlaceholder($exp, 'end_date')) ? 'cv-placeholder' : '' }}">{{ data_get($exp, 'start_date') }}{{ data_get($exp, 'end_date') ? ' - '.data_get($exp, 'end_date') : ' - Present' }}</span>
					</div>
					<div class="cv-list-sub {{ $itemPlaceholder($exp, 'company') ? 'cv-placeholder' : '' }}">{{ data_get($exp, 'company') }}</div>
					@if(data_get($exp, 'description'))
						<div class="cv-list-desc {{ $itemPlaceholder($exp, 'description') ? 'cv-placeholder' : '' }}">{{ $thumbMode ? Str::limit((string) data_get($exp, 'description'), 150) : data_get($exp, 'description') }}</div>
					@endif
				</article>
			@endforeach
		</section>

		<section class="cv-card">
			<h2 class="cv-card-title">Education</h2>
			@foreach($educations as $edu)
				<article class="cv-list-item">
					<div class="cv-list-head">
						<p class="cv-list-role {{ $itemPlaceholder($edu, 'school') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'school') }}</p>
						<span class="cv-list-date {{ $itemPlaceholder($edu, 'year') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'year') }}</span>
					</div>
					<div class="cv-list-sub {{ $itemPlaceholder($edu, 'degree') ? 'cv-placeholder' : '' }}">{{ data_get($edu, 'degree') }}</div>
				</article>
			@endforeach

			<h2 class="cv-card-title" style="margin-top:12px;">Skills</h2>
			<div class="cv-skill-row">
				@foreach($skills as $skill)
					<span class="cv-skill-badge {{ $itemPlaceholder($skill, 'name') ? 'cv-placeholder' : '' }}">{{ data_get($skill, 'name') }}</span>
				@endforeach
			</div>
		</section>
	</div>
</div>
@endsection
